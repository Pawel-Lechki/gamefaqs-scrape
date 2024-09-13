<x-app-layout>
    <div class="d-flex p-2 flex-column justify-content-center align-items-center w-100" style="margin-top: 50px">
        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        <h1>Enter Platform Name</h1>
        <form action="{{ route('fetchData') }}" method="POST" class="d-flex flex-column">
            @csrf
            <div class="mb-3">
                <label for="platform" class="form-label">Platform:</label>
                <input type="text" id="platform" name="platform" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="platform" class="form-label">Page:</label>
                <input type="text" id="page" name="page" class="form-control">
            </div>

            <button type="submit" class="btn btn-dark">Fetch Data</button>
        </form>
    </div>
</x-app-layout>
