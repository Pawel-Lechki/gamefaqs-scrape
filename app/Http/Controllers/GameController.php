<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GameController extends Controller
{
    public function showForm()
    {
        return view('platform_form');
    }

    public function fetchData(Request $request)
    {
        $platform = $request->input('platform');
        $page = $request->input('page', 0);

        if (!$platform) {
            return back()->with('error', 'Platform parameter is missing');
        }
        Session::put('platform', $platform);
        Session::put('page', $page);
        $url = "https://gamefaqs.gamespot.com/{$platform}/category/999-all";

        $games = [];
        $totalPages = 1;

        $response = Http::get($url . "?page=" . $page);

        if (!$response->successful()) {
            return back()->with('error', 'Failed to fetch data from GameFAQs.');
        }

        $content = $response->body();

        $dom = new \DOMDocument();
        @$dom->loadHTML($content);

        $xpath = new \DOMXPath($dom);

        $gameNodes = $xpath->query('//td[@class="rtitle"]/a');
        foreach ($gameNodes as $node) {
            $gameUrl = "https://gamefaqs.gamespot.com" . $node->getAttribute('href');
            $gameName = trim($node->textContent);
            $gameData = $this->fetchGameDetails($gameUrl, $gameName);
            if ($gameData) {
                $games[] = $gameData;
            }
        }

        Session::put('games', $games);

        return view('games_list', ['games' => $games]);
    }

    private function fetchGameDetails(string $url, string $gameName): ?array
    {
        $response = Http::get($url);
        $content = $response->body();

        $dom = new \DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new \DOMXPath($dom);

//        $genre = $this->getXPathTextArray($xpath, '//td[contains(text(), "Genre")]/following-sibling::td//a');
//        $releaseDate = $this->formatDate($this->getXPathText($xpath, '//td[contains(text(), "Release Date")]/following-sibling::td'));
        $genre = $this->getXPathTextArray($xpath, '//ol[@class="list flex col1 nobg"]//li[2]//b[contains(text(), "Genre")]/following-sibling::a');
        // Obsługa różnych przypadków Developer i Publisher
        $developer = '';
        $publisher = '';

        // Obsługa Developer/Publisher w jednym
        $devPubCombined = $xpath->query('//div[@class="content"]/b[contains(text(), "Developer/Publisher")]/following-sibling::a');
        if ($devPubCombined->length > 0) {
            $developer = trim($devPubCombined->item(0)->textContent);
            $publisher = $developer; // W tym przypadku zakładamy, że deweloper jest też wydawcą
        } else {
            // Developer i Publisher osobno
            $developer = $this->getXPathText($xpath, '//div[@class="content"]/b[contains(text(), "Developer")]/following-sibling::a');
            $publisher = $this->getXPathText($xpath, '//div[@class="content"]/b[contains(text(), "Publisher")]/following-sibling::a');
        }

        $dateText = $this->getXPathText($xpath, '//div[@class="content"]/b[contains(text(), "Release:")]/following-sibling::a[1]');
        $releaseDate = $this->formatDate($dateText);

        return [
            'name' => $gameName,
            'url' => $url,
            'genre1' => $genre[0] ?? 'N/A',
            'genre2' => $genre[1] ?? 'N/A',
            'genre3' => $genre[2] ?? 'N/A',
            'genre4' => $genre[3] ?? 'N/A',
            'release_date' => $releaseDate,
            'developer' => $developer ?: 'N/A',
            'publisher' => $publisher ?: 'N/A',
        ];
    }

    private function getXPathText(\DOMXPath $xpath, string $query): string
    {
        $node = $xpath->query($query)->item(0);
        return $node ? trim($node->textContent) : 'N/A';
    }

    private function getXPathTextArray(\DOMXPath $xpath, string $query): array
    {
        $nodes = $xpath->query($query);
        $values = [];
        foreach ($nodes as $node) {
            $values[] = trim($node->textContent);
        }
        return $values;
    }

    private function formatDate(string $date): string
    {
        if (stripos($date, 'Canceled') !== false) {
            return 'Canceled';
        }
        /// Sprawdzenie dla formatu: Miesiąc dzień, rok (np. March 15, 2022)
        if (preg_match('/^([A-Za-z]+) (\d{1,2}), (\d{4})$/', $date, $matches)) {
            $formattedDate = DateTime::createFromFormat('F j, Y', $date);
            return $formattedDate ? $formattedDate->format('d.m.Y') : 'N/A';
        }

        // Sprawdzenie dla formatu: Miesiąc, rok (np. February 2005)
        if (preg_match('/^([A-Za-z]+) (\d{4})$/', $date, $matches)) {
            $formattedDate = DateTime::createFromFormat('F Y', $date);
            return $formattedDate ? '01.' . $formattedDate->format('m.Y') : 'N/A';
        }

        // Sprawdzenie dla formatu: rok (np. 2005)
        if (preg_match('/^(\d{4})$/', $date, $matches)) {
            return '01.01.' . $matches[1];
        }

        // Debugowanie w przypadku nieudanego dopasowania
//        echo "Nieznany format daty: $date\n";
        return 'N/A';
    }

    public function exportToXlsx(Request $request)
    {
        $games = Session::get('games', []);
        $platform = Session::get('platform');
        $page = Session::get('page'); // Domyślnie na stronie 0

        if (empty($games)) {
            return back()->with('error', 'No data available to export.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'URL');
        $sheet->setCellValue('C1', 'Genre 1');
        $sheet->setCellValue('D1', 'Genre 2');
        $sheet->setCellValue('E1', 'Genre 3');
        $sheet->setCellValue('F1', 'Genre 4');
        $sheet->setCellValue('G1', 'Release Date');
        $sheet->setCellValue('H1', 'Developer');
        $sheet->setCellValue('I1', 'Publisher');

        $row = 2;
        foreach ($games as $game) {
            $sheet->setCellValue('A' . $row, $game['name']);
            $sheet->setCellValue('B' . $row, $game['url']);
            $sheet->setCellValue('C' . $row, $game['genre1']);
            $sheet->setCellValue('D' . $row, $game['genre2']);
            $sheet->setCellValue('E' . $row, $game['genre3']);
            $sheet->setCellValue('F' . $row, $game['genre4']);
            $sheet->setCellValue('G' . $row, $game['release_date']);
            $sheet->setCellValue('H' . $row, $game['developer']);
            $sheet->setCellValue('I' . $row, $game['publisher']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $date = date('Y-m-d h-i-s');
        $fileName = "{$platform}.{$page}-{$date}.xlsx"; // Ustal nazwę pliku
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
