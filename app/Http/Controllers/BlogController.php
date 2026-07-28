<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = BlogPost::query()
            ->published()
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
            'seo' => [
                'title' => 'Latest Blog Articles',
                'description' => 'Latest insights, guides and updates.',
                'canonical' => route('blog.index'),
                'og_type' => 'website',
                'og_image' => asset('images/og-default.jpg'),
                'robots' => 'index, follow',
            ],
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = $post->relatedPosts(3);
        $canonical = route('blog.show', $post);
        $ogImage = $post->featured_image_url ?? asset('images/og-default.jpg');

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'shareUrls' => $post->shareUrls(),
            'seo' => [
                'title' => $post->seoTitle(),
                'description' => $post->seoDescription(),
                'canonical' => $canonical,
                'og_type' => 'article',
                'og_image' => $ogImage,
                'robots' => 'index, follow',
                'article' => [
                    'published_time' => optional($post->published_at)->toAtomString(),
                    'modified_time' => optional($post->updated_at)->toAtomString(),
                    'author' => $post->author,
                    'publisher' => config('app.name'),
                ],
            ],
        ]);
    }
}
