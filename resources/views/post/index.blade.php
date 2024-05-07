@extends('auth.layouts')

@section('content')
    <div class="container mt-5">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h3>All Posts</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="{{ route('post.create') }}" class="btn btn-warning">Add Post</a>
            </div>
        </div>

        @if ($posts->isEmpty())
            <p>No posts available.</p>
        @else
            <div class="row">
                @foreach ($posts as $post)
                    @php
                        $user = Auth::user();
                        $userLiked = $post
                            ->likes()
                            ->where('user_id', $user->id)
                            ->exists();
                    @endphp
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">{{ $post->title }}</h5>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit($post->description, 100) }}</p>
                                <div class="d-flex justify-content-between">
                                    <div style="display: flex; gap: 3px; align-items: center;">
                                        <i class="{{ $userLiked ? 'fa-solid' : 'fa-regular' }} fa-heart"
                                            style="cursor: pointer;" onclick="toggleLike({{ $post->id }}, this)">
                                        </i>
                                        <span class="like-count">{{ $post->likes()->count() }}</span> <!-- Like count -->
                                    </div>
                                    <button class="btn btn-danger" onclick="deletePost({{ $post->id }})">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>

<script>
    function toggleLike(postId, element) {
        // alert(postId)
        const isLiked = $(element).hasClass("fa-solid");
        const likeCountSpan = $(element).siblings(".like-count");
        const currentCount = parseInt(likeCountSpan.text());
        const method = isLiked ? 'DELETE' : 'POST';
        $.ajax({
            url: `/posts/${postId}/like`,
            method: method,
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                if (method === 'POST') {
                    $(element).removeClass("fa-regular").addClass("fa-solid");
                    likeCountSpan.text(currentCount + 1);
                    // toastr.success("Post liked!");
                } else {
                    $(element).removeClass("fa-solid").addClass("fa-regular");
                    likeCountSpan.text(currentCount - 1);
                    // toastr.success("Post unliked!");
                }
            },
            error: function(xhr, status, error) {
                toastr.error("An error occurred. Please try again.");
            },
        });
    }

    function deletePost(postId) {
        if (confirm("Are you sure you want to delete this post?")) {
            $.ajax({
                url: `/posts/${postId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    toastr.success("Post deleted successfully");
                    setTimeout(() => location.reload(), 1000);
                },
                error: function() {
                    toastr.error("An error occurred. Please try again.");
                },
            });
        }
    }
</script>
