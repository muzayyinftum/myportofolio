<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .tags {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .count {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Daftar Posts</h1>
        
        <div class="count">
            Total: {{ $posts->count() }} posts
        </div>

        @if($posts->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Author</th>
                        <th>Tags</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td>{{ $post->id }}</td>
                            <td><strong>{{ $post->title }}</strong></td>
                            <td>{{ Str::limit($post->content, 50) }}</td>
                            <td>{{ $post->author }}</td>
                            <td>
                                @if($post->tags && count($post->tags) > 0)
                                    <div class="tags">
                                        @foreach($post->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td>{{ $post->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">
                <p>Belum ada data posts.</p>
                <p>Gunakan API atau Tinker untuk menambahkan data.</p>
            </div>
        @endif
    </div>
</body>
</html>


