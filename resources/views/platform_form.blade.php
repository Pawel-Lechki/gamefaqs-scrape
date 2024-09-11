<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Data</title>
</head>
<body>
<h1>Enter Platform Name</h1>
<form action="{{ route('fetchData') }}" method="POST">
    @csrf
    <label for="platform">Platform:</label>
    <input type="text" id="platform" name="platform" required>
    <label for="platform">Page:</label>
    <input type="text" id="page" name="page">
    <button type="submit">Fetch Data</button>
</form>
</body>
</html>
