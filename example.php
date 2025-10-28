<?php
    /**
     * Example usage of SoftMapper library
     * This file demonstrates how to use the Post model
     */
    require_once 'Post.php';

    // Create a new Post instance
    $post = new Post();

    // Example 1: Insert a new post
    $post->columns['title'] = 'My First Post';
    $post->columns['body'] = 'This is the content of my first post.';
    $result = $post->insert();

    if ($result) {
        echo "Post inserted successfully!\n";
    }

    // Example 2: Find a post by ID
    $found_post = $post->find(1);
    if ($found_post) {
        echo "Found post: " . $found_post->title . "\n";
    }

    // Example 3: Get all posts
    $all_posts = $post->all()->getAll();
    foreach ($all_posts as $p) {
        echo "Post: " . $p->title . "\n";
    }

    // Example 4: Update a post
    $post->columns['title'] = 'Updated Title';
    $post->columns['body'] = 'Updated content';
    $post->update()->where([['id', '=', 1]])->execute();

    // Example 5: Delete a post
    $post->delete()->where([['id', '=', 1]])->execute();
