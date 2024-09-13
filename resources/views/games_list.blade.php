<x-app-layout>
    <div class="p-2">
    <table class="table">
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
                <td><a href="{{ $game['url'] }}" target="_blank">{{ $game['name'] }}</a></td>
                <td><a href="{{ $game['url'] }}" target="_blank">{{ $game['url'] }}</a></td>
                @for ($i = 0; $i < 4; $i++)
                    @php
                        $genre = $game['genres'][$i] ?? ['text' => 'N/A', 'link' => null];
                    @endphp
                    <td>
                        @if($genre['link'])
                            <a href="{{ $genre['link'] }}" target="_blank">{{ $genre['text'] }}</a>
                        @else
                            {{ $genre['text'] }}
                        @endif
                    </td>
                @endfor
{{--                <td>--}}
{{--                    @if($game['release_date_link'])--}}
{{--                        <a href="{{ $game['release_date_link'] }}" target="_blank">{{ $game['release_date'] }}</a>--}}
{{--                    @else--}}
{{--                        {{ $game['release_date'] }}--}}
{{--                    @endif--}}
{{--                </td>--}}
                <td>{{ $game['release_date'] }}</td>
                <td>
                    @if($game['developer_link'])
                        <a href="{{ $game['developer_link'] }}" target="_blank">{{ $game['developer'] }}</a>
                    @else
                        {{ $game['developer'] }}
                    @endif
                </td>
                <td>
                    @if($game['publisher_link'])
                        <a href="{{ $game['publisher_link'] }}" target="_blank">{{ $game['publisher'] }}</a>
                    @else
                        {{ $game['publisher'] }}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="d-flex w-100 justify-content-between align-items-center p-5">
        <a href="{{ route('showForm') }}" class="btn btn-dark">Back</a>
        <a href="{{ route('exportXlsx') }}" class="btn btn-success">Export to XLSX</a>
    </div>
</x-app-layout>
