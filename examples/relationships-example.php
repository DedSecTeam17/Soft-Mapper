<?php
    /**
     * ORM Relationships Example
     * 
     * This file demonstrates how to use the relationship features in Soft-Mapper.
     * It shows 1:1, 1:N, and N:N relationships with practical examples.
     */

    require_once __DIR__ . '/User.php';
    require_once __DIR__ . '/Post.php';
    require_once __DIR__ . '/Comment.php';
    require_once __DIR__ . '/Tag.php';
    require_once __DIR__ . '/UserProfile.php';

    echo "=== Soft-Mapper ORM Relationships Examples ===\n\n";

    // ============================================
    // Example 1: One-to-Many (1:N) - User has many Posts
    // ============================================
    echo "Example 1: One-to-Many Relationship (User has many Posts)\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $user = new User();
        
        // Get a user and load their posts
        $user_with_posts = $user->find(1);
        
        if ($user_with_posts) {
            // Load the posts relationship
            $user->loadRelation('posts', $user_with_posts);
            
            echo "User: " . $user_with_posts->name . "\n";
            echo "Number of posts: " . count($user_with_posts->posts) . "\n";
            
            if (!empty($user_with_posts->posts)) {
                echo "Posts:\n";
                foreach ($user_with_posts->posts as $post) {
                    echo "  - " . $post->title . "\n";
                }
            }
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Note: Database connection required to run this example\n\n";
    }

    // ============================================
    // Example 2: Belongs To (inverse of 1:N) - Post belongs to User
    // ============================================
    echo "Example 2: Belongs To Relationship (Post belongs to User)\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $post = new Post();
        
        // Get a post and load its author
        $post_with_author = $post->find(1);
        
        if ($post_with_author) {
            // Load the user relationship
            $post->loadRelation('user', $post_with_author);
            
            echo "Post: " . $post_with_author->title . "\n";
            if ($post_with_author->user) {
                echo "Author: " . $post_with_author->user->name . "\n";
            }
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Note: Database connection required to run this example\n\n";
    }

    // ============================================
    // Example 3: Has One (1:1) - User has one Profile
    // ============================================
    echo "Example 3: One-to-One Relationship (User has one Profile)\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $user = new User();
        
        // Get a user and load their profile
        $user_with_profile = $user->find(1);
        
        if ($user_with_profile) {
            // Load the profile relationship
            $user->loadRelation('profile', $user_with_profile);
            
            echo "User: " . $user_with_profile->name . "\n";
            if ($user_with_profile->profile) {
                echo "Bio: " . $user_with_profile->profile->bio . "\n";
            }
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Note: Database connection required to run this example\n\n";
    }

    // ============================================
    // Example 4: Many-to-Many (N:N) - Post belongs to many Tags
    // ============================================
    echo "Example 4: Many-to-Many Relationship (Post has many Tags)\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $post = new Post();
        
        // Get a post and load its tags
        $post_with_tags = $post->find(1);
        
        if ($post_with_tags) {
            // Load the tags relationship
            $post->loadRelation('tags', $post_with_tags);
            
            echo "Post: " . $post_with_tags->title . "\n";
            if (!empty($post_with_tags->tags)) {
                echo "Tags: ";
                $tag_names = array_map(function($tag) { return $tag->name; }, $post_with_tags->tags);
                echo implode(', ', $tag_names) . "\n";
            }
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Note: Database connection required to run this example\n\n";
    }

    // ============================================
    // Example 5: Eager Loading (Performance Optimization)
    // ============================================
    echo "Example 5: Eager Loading Multiple Relationships\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $post = new Post();
        
        // Load posts with user and comments in one query (eager loading)
        $posts = $post->with(['user', 'comments'])->all()->getAll();
        
        echo "Loaded " . count($posts) . " posts with users and comments\n";
        
        if (!empty($posts)) {
            foreach ($posts as $p) {
                echo "\nPost: " . $p->title . "\n";
                if (isset($p->user)) {
                    echo "  Author: " . $p->user->name . "\n";
                }
                if (isset($p->comments)) {
                    echo "  Comments: " . count($p->comments) . "\n";
                }
            }
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Note: Database connection required to run this example\n\n";
    }

    // ============================================
    // Example 6: Attach/Detach (Many-to-Many Operations)
    // ============================================
    echo "Example 6: Attach and Detach Tags to Posts\n";
    echo str_repeat("-", 60) . "\n";
    
    echo "Code example:\n";
    echo "\$post = new Post();\n";
    echo "\n";
    echo "// Attach tag with ID 5 to post with ID 1\n";
    echo "\$post->attach(1, 5, 'tags');\n";
    echo "\n";
    echo "// Detach tag with ID 5 from post with ID 1\n";
    echo "\$post->detach(1, 'tags', 5);\n";
    echo "\n";
    echo "// Detach all tags from post with ID 1\n";
    echo "\$post->detach(1, 'tags');\n";
    echo "\n";
    echo "// Sync tags (replace all existing tags with new ones)\n";
    echo "\$post->sync(1, [1, 2, 3], 'tags');\n";
    echo "\n";

    // ============================================
    // Example 7: Complex Query with Relationships
    // ============================================
    echo "Example 7: Complex Query - Posts with Comments Count\n";
    echo str_repeat("-", 60) . "\n";
    
    try {
        $post = new Post();
        
        // Get published posts ordered by date
        $posts = $post->all()
            ->where([['status', '=', 'published']])
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->getAll();
        
        echo "Recent published posts:\n";
        foreach ($posts as $p) {
            // Load comments for each post
            $post->loadRelation('comments', $p);
            
            echo "  - " . $p->title;
            if (isset($p->comments)) {
                echo " (" . count($p->comments) . " comments)";
            }
            echo "\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Note: Database connection required to run this example\n\n";
    }

    // ============================================
    // Example 8: Creating Related Records
    // ============================================
    echo "Example 8: Creating Related Records\n";
    echo str_repeat("-", 60) . "\n";
    
    echo "Code example:\n";
    echo "\$user = new User();\n";
    echo "\$user->columns = ['name' => 'John Doe', 'email' => 'john@example.com'];\n";
    echo "\$user->insert();\n";
    echo "\$user_id = \$user->lastInsertId();\n";
    echo "\n";
    echo "\$post = new Post();\n";
    echo "\$post->columns = [\n";
    echo "    'user_id' => \$user_id,\n";
    echo "    'title' => 'My First Post',\n";
    echo "    'body' => 'This is my first post!',\n";
    echo "    'status' => 'published'\n";
    echo "];\n";
    echo "\$post->insert();\n";
    echo "\n";

    // ============================================
    // Database Schema Information
    // ============================================
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Database Schema Requirements\n";
    echo str_repeat("=", 60) . "\n\n";
    
    echo "To use these examples, create the following tables:\n\n";
    
    echo "-- Users table\n";
    echo "CREATE TABLE users (\n";
    echo "    id INT PRIMARY KEY AUTO_INCREMENT,\n";
    echo "    name VARCHAR(255) NOT NULL,\n";
    echo "    email VARCHAR(255) UNIQUE NOT NULL,\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n";
    echo ");\n\n";
    
    echo "-- Posts table\n";
    echo "CREATE TABLE posts (\n";
    echo "    id INT PRIMARY KEY AUTO_INCREMENT,\n";
    echo "    user_id INT NOT NULL,\n";
    echo "    title VARCHAR(255) NOT NULL,\n";
    echo "    body TEXT,\n";
    echo "    status VARCHAR(50) DEFAULT 'draft',\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
    echo "    deleted_at TIMESTAMP NULL,\n";
    echo "    FOREIGN KEY (user_id) REFERENCES users(id)\n";
    echo ");\n\n";
    
    echo "-- Comments table\n";
    echo "CREATE TABLE comments (\n";
    echo "    id INT PRIMARY KEY AUTO_INCREMENT,\n";
    echo "    post_id INT NOT NULL,\n";
    echo "    user_id INT NOT NULL,\n";
    echo "    body TEXT NOT NULL,\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
    echo "    FOREIGN KEY (post_id) REFERENCES posts(id),\n";
    echo "    FOREIGN KEY (user_id) REFERENCES users(id)\n";
    echo ");\n\n";
    
    echo "-- Tags table\n";
    echo "CREATE TABLE tags (\n";
    echo "    id INT PRIMARY KEY AUTO_INCREMENT,\n";
    echo "    name VARCHAR(100) UNIQUE NOT NULL,\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n";
    echo ");\n\n";
    
    echo "-- Post-Tag pivot table (for N:N relationship)\n";
    echo "CREATE TABLE post_tag (\n";
    echo "    post_id INT NOT NULL,\n";
    echo "    tag_id INT NOT NULL,\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    PRIMARY KEY (post_id, tag_id),\n";
    echo "    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,\n";
    echo "    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE\n";
    echo ");\n\n";
    
    echo "-- User Profiles table (for 1:1 relationship)\n";
    echo "CREATE TABLE user_profiles (\n";
    echo "    id INT PRIMARY KEY AUTO_INCREMENT,\n";
    echo "    user_id INT UNIQUE NOT NULL,\n";
    echo "    bio TEXT,\n";
    echo "    avatar VARCHAR(255),\n";
    echo "    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n";
    echo "    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
    echo "    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE\n";
    echo ");\n\n";
    
    echo "=== End of Examples ===\n";
