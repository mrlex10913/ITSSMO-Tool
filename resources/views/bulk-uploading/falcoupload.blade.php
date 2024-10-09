<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bulk Upload</title>
</head>
<body>
    <form action="{{route('falco.post')}}" method="post" enctype="multipart/form-data">
        @csrf
    <input type="file" name="excel_file" id="">
    <input type="submit">
    </form>
</body>
</html>
