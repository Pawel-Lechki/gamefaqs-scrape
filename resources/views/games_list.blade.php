<x-app-layout>
    <table class="table p-2">
        <thead>
        <tr>
            <th>Name</th>
            <th>URL</th>
            <th>Genre 1</th>
            <th>Genre 2</th>
            <th>Genre 3</th>
            <th>Genre 4</th>
            <th>Release Date</th>
            <th>Developer</th>
            <th>Publisher</th>
        </tr>
        </thead>
        <tbody>
        @foreach($games as $game)
            <tr>
                <td>{{ $game['name'] }}</td>
                <td><a href="{{ $game['url'] }}" target="_blank">Link</a></td>
                <td>{{ $game['genre1'] }}</td>
                <td>{{ $game['genre2'] }}</td>
                <td>{{ $game['genre3'] }}</td>
                <td>{{ $game['genre4'] }}</td>
                <td>{{ $game['release_date'] }}</td>
                <td>{{ $game['developer'] }}</td>
                <td>{{ $game['publisher'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="d-flex w-100 justify-content-between align-items-center p-5">
        <a href="{{ route('showForm') }}" class="btn btn-dark">Back</a>
        <a href="{{ route('exportXlsx') }}" class="btn btn-success">Export to XLSX</a>
    </div>
</x-app-layout>
