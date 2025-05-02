DROP SCHEMA IF EXISTS lbaw24145 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw24145;
SET search_path TO lbaw24145;


-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE notification_status AS ENUM ('seen','unseen');
CREATE TYPE request_status AS ENUM ('pending', 'accepted', 'rejected');



-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR UNIQUE NOT NULL,
    email VARCHAR UNIQUE NOT NULL,
    password VARCHAR NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_DATE,
    remember_token VARCHAR
);

CREATE TABLE password_reset_tokens ( --esta tabela tem de ter este nome senao o laravel n deixa fazer reset a pass
    email VARCHAR NOT NULL,
    token VARCHAR NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE profile (
    id INT PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    full_name VARCHAR(100) NOT NULL,
    profile_pic TEXT,
    bio TEXT,
    admin BOOLEAN DEFAULT FALSE,
    banned BOOLEAN DEFAULT FALSE,
    is_public BOOLEAN DEFAULT FALSE
);

CREATE TABLE "group" (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    author_id INT NOT NULL DEFAULT 0 REFERENCES profile(id) ON DELETE SET DEFAULT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE post (
    id SERIAL PRIMARY KEY,
    author_id INT NOT NULL DEFAULT 0 REFERENCES profile(id) ON DELETE SET DEFAULT,
    group_id INT REFERENCES "group"(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    image_url TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE comment (
    id SERIAL PRIMARY KEY,
    author_id INT NOT NULL DEFAULT 0 REFERENCES profile(id) ON DELETE SET DEFAULT,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE group_users (
    user_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    group_id INT NOT NULL REFERENCES "group"(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, group_id)
);

CREATE TABLE user_likes (
    user_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    UNIQUE(user_id, post_id)
);

CREATE TABLE user_likes_comments (
    user_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    comment_id INT NOT NULL REFERENCES comment(id) ON DELETE CASCADE,
    UNIQUE(user_id, comment_id)
);

CREATE TABLE user_friends (
    user_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    friend_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    UNIQUE(user_id, friend_id)
);

CREATE TABLE user_blocked (
    user_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    blocked_user_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    UNIQUE(user_id, blocked_user_id)
);

CREATE TABLE share (
    id SERIAL PRIMARY KEY,
    author_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notif (
    id SERIAL PRIMARY KEY,
    receiver_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    sender_id INT NOT NULL REFERENCES profile(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    notification_type VARCHAR(50) NOT NULL,
    notification_status notification_status NOT NULL DEFAULT 'unseen'

);

CREATE TABLE friend_request_notification (
    notification_id INT NOT NULL REFERENCES notif(id) ON DELETE CASCADE,
    request_status request_status NOT NULL DEFAULT 'pending'
);

CREATE TABLE group_invite_notification (
    notification_id INT NOT NULL REFERENCES notif(id) ON DELETE CASCADE,
    group_id INT NOT NULL REFERENCES "group"(id) ON DELETE CASCADE,
    request_status request_status NOT NULL DEFAULT 'pending'
);

CREATE TABLE post_like_notification (
    notification_id INT NOT NULL REFERENCES notif(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE
);

CREATE TABLE comment_like_notification (
    notification_id INT NOT NULL REFERENCES notif(id) ON DELETE CASCADE,
    comment_id INT NOT NULL REFERENCES comment(id) ON DELETE CASCADE
);

CREATE TABLE post_comment_notification (
    notification_id INT NOT NULL REFERENCES notif(id) ON DELETE CASCADE,
    comment_id INT NOT NULL REFERENCES comment(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE
);

CREATE TABLE post_share_notification (
    notification_id INT NOT NULL REFERENCES notif(id) ON DELETE CASCADE,
    post_id INT NOT NULL REFERENCES post(id) ON DELETE CASCADE,
    share_id INT NOT NULL REFERENCES share(id) ON DELETE CASCADE
);




-----------------------------------------
-- Performance Indices
-----------------------------------------

CREATE INDEX idx_users_name ON users(name);
CREATE INDEX idx_post_author_id ON post(author_id);
CREATE INDEX idx_notification_receiver_id ON notif(receiver_id);


-----------------------------------------
-- FTS Indices
-----------------------------------------

-- 1. Post Search
-- Adicionamos coluna tsvector à tabela post
ALTER TABLE post 
ADD COLUMN tsvectors TSVECTOR;

-- Criar função de atualização da tabela de pesquisas em posts
CREATE OR REPLACE FUNCTION post_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('english', COALESCE(NEW.content, '')), 'A')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.content <> OLD.content) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', COALESCE(NEW.content, '')), 'A')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

-- Criar trigger de atualização da tabela de pesquisas em posts
CREATE TRIGGER post_search_update
    BEFORE INSERT OR UPDATE ON post
    FOR EACH ROW
    EXECUTE FUNCTION post_search_update();

-- Criar índice GIN para pesquisa em posts
CREATE INDEX idx_post_search 
    ON post 
    USING GIN(tsvectors);

-- Todos os nossos full text search indexes funcionam mais ou menos como este


-- 2. Profile Search
ALTER TABLE profile 
ADD COLUMN tsvectors TSVECTOR;

CREATE OR REPLACE FUNCTION profile_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('english', COALESCE(NEW.full_name, '')), 'A') ||
            setweight(to_tsvector('english', COALESCE(NEW.bio, '')), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.full_name <> OLD.full_name OR NEW.bio <> OLD.bio) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', COALESCE(NEW.full_name, '')), 'A') ||
                setweight(to_tsvector('english', COALESCE(NEW.bio, '')), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER profile_search_update
    BEFORE INSERT OR UPDATE ON profile
    FOR EACH ROW
    EXECUTE FUNCTION profile_search_update();

CREATE INDEX idx_profile_search 
    ON profile 
    USING GIN(tsvectors);

-- 3. Group Search
ALTER TABLE "group"
ADD COLUMN tsvectors TSVECTOR;

CREATE OR REPLACE FUNCTION group_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('english', COALESCE(NEW.name, '')), 'A') ||
            setweight(to_tsvector('english', COALESCE(NEW.description, '')), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', COALESCE(NEW.name, '')), 'A') ||
                setweight(to_tsvector('english', COALESCE(NEW.description, '')), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER group_search_update
    BEFORE INSERT OR UPDATE ON "group"
    FOR EACH ROW
    EXECUTE FUNCTION group_search_update();

CREATE INDEX idx_group_search 
    ON "group" 
    USING GIN(tsvectors);


-- 4. Comment Search
ALTER TABLE comment 
ADD COLUMN tsvectors TSVECTOR;

CREATE OR REPLACE FUNCTION comment_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('english', COALESCE(NEW.content, '')), 'A')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.content <> OLD.content) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', COALESCE(NEW.content, '')), 'A')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER comment_search_update
    BEFORE INSERT OR UPDATE ON comment
    FOR EACH ROW
    EXECUTE FUNCTION comment_search_update();

CREATE INDEX idx_comment_search 
    ON comment 
    USING GIN(tsvectors);




-----------------------------------------
-- Triggers
-----------------------------------------


CREATE OR REPLACE FUNCTION anonymize_user()
RETURNS TRIGGER AS $$
DECLARE
    profile_is_public BOOLEAN;
BEGIN
    SELECT p.is_public INTO profile_is_public FROM profile p WHERE p.id = OLD.id;

    IF profile_is_public THEN
        UPDATE post 
        SET author_id = 1
        WHERE author_id = OLD.id;
        
        UPDATE comment
        SET author_id = 1
        WHERE author_id = OLD.id;
        
        UPDATE share
        SET author_id = 1
        WHERE author_id = OLD.id;
        
        UPDATE "group"
        SET author_id = 1
        WHERE author_id = OLD.id;
    ELSE
        UPDATE post 
        SET author_id = 2
        WHERE author_id = OLD.id;
        
        UPDATE comment
        SET author_id = 2
        WHERE author_id = OLD.id;
        
        UPDATE share
        SET author_id = 2
        WHERE author_id = OLD.id;
        
        UPDATE "group"
        SET author_id = 2
        WHERE author_id = OLD.id;
    END IF;
        
    DELETE FROM group_users
    WHERE user_id = OLD.id;
    
    DELETE FROM user_friends
    WHERE user_id = OLD.id OR friend_id = OLD.id;
    
    DELETE FROM user_blocked
    WHERE user_id = OLD.id OR blocked_user_id = OLD.id;
    
    DELETE FROM user_likes
    WHERE user_id = OLD.id;
    
    DELETE FROM user_likes_comments
    WHERE user_id = OLD.id;
    
    DELETE FROM notif
    WHERE receiver_id = OLD.id;
    
    DELETE FROM profile
    WHERE id = OLD.id;
    
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_delete_user
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION anonymize_user();

CREATE OR REPLACE FUNCTION create_profile()
RETURNS TRIGGER AS $$
DECLARE
    base_name TEXT;
    sufixo INT := 1;
    unique_name TEXT;
BEGIN
    base_name := split_part(NEW.email, '@', 1);
    unique_name := base_name;

    WHILE EXISTS (SELECT 1 FROM profile WHERE full_name = unique_name) LOOP
        unique_name := base_name || sufixo;
        sufixo := sufixo + 1;
    END LOOP;

    INSERT INTO profile (id, full_name, admin, is_public)
    VALUES (NEW.id, unique_name, FALSE, TRUE);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_insert_user
AFTER INSERT ON users
FOR EACH ROW
EXECUTE FUNCTION create_profile();

CREATE OR REPLACE FUNCTION create_post_share_notification()
RETURNS TRIGGER AS $$
DECLARE
    notification_id INT;
    post_author_id INT;
BEGIN
    SELECT author_id INTO post_author_id
    FROM post 
    WHERE id = NEW.post_id;

    IF NEW.author_id = post_author_id THEN
        RETURN NEW;
    END IF;

    INSERT INTO notif (receiver_id, sender_id, notification_type)
    VALUES (post_author_id, NEW.author_id, 'post_share')
    RETURNING id INTO notification_id;

    INSERT INTO post_share_notification (notification_id, post_id, share_id)
    VALUES (notification_id, NEW.post_id, NEW.id);
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_share_insert
AFTER INSERT ON share
FOR EACH ROW
EXECUTE FUNCTION create_post_share_notification();

CREATE OR REPLACE FUNCTION create_post_comment_notification()
RETURNS TRIGGER AS $$
DECLARE
    notification_id INT;
    post_author_id INT;
BEGIN
    SELECT author_id INTO post_author_id
    FROM post 
    WHERE id = NEW.post_id;

    IF NEW.author_id = post_author_id THEN
        RETURN NEW;
    END IF;

    INSERT INTO notif (receiver_id, sender_id, notification_type)
    VALUES (post_author_id, NEW.author_id, 'post_comment')
    RETURNING id INTO notification_id;

    INSERT INTO post_comment_notification (notification_id, comment_id, post_id)
    VALUES (notification_id, NEW.id, NEW.post_id);
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_comment_insert
AFTER INSERT ON comment
FOR EACH ROW
EXECUTE FUNCTION create_post_comment_notification();

CREATE OR REPLACE FUNCTION create_comment_like_notification()
RETURNS TRIGGER AS $$
DECLARE
    notification_id INT;
    comment_author_id INT;
BEGIN
    SELECT author_id INTO comment_author_id
    FROM comment 
    WHERE id = NEW.comment_id;

    IF NEW.user_id = comment_author_id THEN
        RETURN NEW;
    END IF;

    INSERT INTO notif (receiver_id, sender_id, notification_type)
    VALUES (comment_author_id, NEW.user_id, 'comment_like')
    RETURNING id INTO notification_id;

    INSERT INTO comment_like_notification (notification_id, comment_id)
    VALUES (notification_id, NEW.comment_id);
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_comment_like_insert
AFTER INSERT ON user_likes_comments
FOR EACH ROW
EXECUTE FUNCTION create_comment_like_notification();

CREATE OR REPLACE FUNCTION create_post_like_notification()
RETURNS TRIGGER AS $$
DECLARE
    notification_id INT;
    post_author_id INT;
BEGIN
    SELECT author_id INTO post_author_id
    FROM post 
    WHERE id = NEW.post_id;

    IF NEW.user_id = post_author_id THEN
        RETURN NEW;
    END IF;

    INSERT INTO notif (receiver_id, sender_id, notification_type)
    VALUES (post_author_id, NEW.user_id, 'post_like')
    RETURNING id INTO notification_id;

    INSERT INTO post_like_notification (notification_id, post_id)
    VALUES (notification_id, NEW.post_id);
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER after_post_like_insert
AFTER INSERT ON user_likes
FOR EACH ROW
EXECUTE FUNCTION create_post_like_notification();



-- BR01 TRIGGER
CREATE OR REPLACE FUNCTION prevent_duplicate_friend_request()
RETURNS TRIGGER AS $$
DECLARE
    notification_sender_id INT;
    notification_receiver_id INT;
BEGIN
    SELECT n.sender_id, n.receiver_id INTO notification_sender_id, notification_receiver_id
    FROM notif n
    WHERE n.id = NEW.notification_id;

    IF EXISTS (
        SELECT 1
        FROM friend_request_notification frn
        JOIN notif n ON frn.notification_id = n.id
        WHERE n.sender_id = notification_sender_id
        AND n.receiver_id = notification_receiver_id
        AND frn.request_status = 'pending'
        AND frn.notification_id != NEW.notification_id
    ) THEN
        RAISE EXCEPTION 'A pending friend request already exists between these users.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_insert_friend_request
BEFORE INSERT ON friend_request_notification
FOR EACH ROW
EXECUTE FUNCTION prevent_duplicate_friend_request();

-- BR02 TRIGGER
CREATE OR REPLACE FUNCTION second_post_like()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM user_likes
        WHERE user_id = NEW.user_id
        AND post_id = NEW.post_id
    ) THEN
        DELETE FROM user_likes
        WHERE user_id = NEW.user_id
        AND post_id = NEW.post_id;
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_insert_user_secondlikes_post
BEFORE INSERT ON user_likes
FOR EACH ROW
EXECUTE FUNCTION second_post_like();

-- BR03 TRIGGER
CREATE OR REPLACE FUNCTION second_comment_like()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM user_likes_comments
        WHERE user_id = NEW.user_id
        AND comment_id = NEW.comment_id
    ) THEN
        DELETE FROM user_likes_comments
        WHERE user_id = NEW.user_id
        AND comment_id = NEW.comment_id;
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_insert_user_secondlikes_comment
BEFORE INSERT ON user_likes_comments
FOR EACH ROW
EXECUTE FUNCTION second_comment_like();


-- BR04 TRIGGER
CREATE OR REPLACE FUNCTION check_post_empty()
RETURNS TRIGGER AS $$
BEGIN
    IF COALESCE(NEW.content, '') = '' AND COALESCE(NEW.image_url, '') = '' THEN
        RAISE EXCEPTION 'The post is empty. It should have media or text.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER before_insert_or_update_post
BEFORE INSERT OR UPDATE ON post
FOR EACH ROW
EXECUTE FUNCTION check_post_empty();


-----------------------------------------
-- DB POPULATION
-----------------------------------------

-- Anonymous user
INSERT INTO users (name, email, password, created_at, remember_token) VALUES
('AnonymousPublic', 'anonymouspub@example.com', '$2a$12$PRD3/G9VMd70s2uN/eZZiOzUCLwtKEdELd1adxiU4Yh4P0ewVMAGC', CURRENT_TIMESTAMP, NULL);
-- Password: anonymous
update profile set is_public = true, full_name = '<DELETED>', profile_pic = 'https://ui-avatars.com/api/?name=Deleted+User&color=7F9CF5&background=EBF4FF' where id = 1;

INSERT INTO users (name, email, password, created_at, remember_token) VALUES
('AnonymousPrivate', 'anonymouspriv@example.com', '$2a$12$PRD3/G9VMd70s2uN/eZZiOzUCLwtKEdELd1adxiU4Yh4P0ewVMAGC', CURRENT_TIMESTAMP, NULL);
-- Password: anonymous
update profile set is_public = false, full_name = '<DELETED>', profile_pic = 'https://ui-avatars.com/api/?name=Deleted+User&color=7F9CF5&background=EBF4FF' where id = 2;

-- Admin user
INSERT INTO users (name, email, password, created_at, remember_token) VALUES
('Admin', 'admin@example.com', '$2a$12$bCCRKe3wtbZdlLni3j8FweIdauIPJXgpQhX35k.X.la/0SfzTYMIK', CURRENT_TIMESTAMP, NULL);
-- Password: adminpassword

update profile set admin = true where id = 3;
update profile set profile_pic = 'https://ui-avatars.com/api/?name=Admin&color=FFFFFF&background=000000' where id = 3;



-- Regular users
DO $$
DECLARE
    user_count INT := 100; -- Number of users to create
    i INT;
    names TEXT[] := ARRAY[
        'Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Hank', 'Ivy', 'Jack', 'Kathy', 'Leo', 'Mona', 'Nina', 'Oscar', 'Paul', 'Quincy', 'Rita', 'Sam', 'Tina', 'Uma', 'Victor', 'Wendy', 'Xander', 'Yara', 'Zane',
        'Aaron', 'Beth', 'Carl', 'Diana', 'Ethan', 'Fiona', 'George', 'Holly', 'Ian', 'Jill', 'Kevin', 'Laura', 'Mike', 'Nora', 'Oliver', 'Pam', 'Quinn', 'Rachel', 'Steve', 'Tracy', 'Ursula', 'Vince', 'Will', 'Xena', 'Yvonne', 'Zack',
        'Adam', 'Bella', 'Chris', 'Donna', 'Eric', 'Faith', 'Gabe', 'Hannah', 'Isaac', 'Jen', 'Kyle', 'Lily', 'Matt', 'Nate', 'Owen', 'Paula', 'Ralph', 'Sara', 'Tom', 'Tara', 'Ulysses', 'Vera', 'Walt', 'Ximena', 'Yosef', 'Zara',
        'Aiden', 'Brooke', 'Cameron', 'Derek', 'Elena', 'Felix', 'Gina', 'Harry', 'Iris', 'Jake', 'Kim', 'Liam', 'Megan', 'Noah', 'Piper', 'Reed', 'Sophie', 'Tyler', 'Violet', 'Wyatt', 'Zoe', 'Alex', 'Brianna', 'Connor', 'Dylan',
        'Emma', 'Finn', 'Gabby', 'Hunter', 'Isla', 'James', 'Kara', 'Logan', 'Mia', 'Nolan', 'Peyton', 'Riley', 'Savannah', 'Theo', 'Victoria', 'Wesley', 'Zachary', 'Ava', 'Blake', 'Carter', 'Daisy', 'Eli', 'Freya', 'Gavin', 'Hazel',
        'Jasper', 'Kylie', 'Lucas', 'Mila', 'Nathan', 'Parker', 'Ruby', 'Sebastian', 'Tessa', 'Vince', 'Willow', 'Xander', 'Yara', 'Zane', 'Alyssa', 'Brandon', 'Chloe', 'Daniel', 'Evelyn', 'Fiona', 'Grayson', 'Hannah', 'Isaiah',
        'Jade', 'Kaden', 'Leah', 'Mason', 'Nina', 'Paige', 'Ryan', 'Scarlett', 'Tristan', 'Valerie', 'Wade', 'Ximena', 'Yosef', 'Zoey', 'Amelia', 'Bryce', 'Caitlyn', 'Damon', 'Eliza', 'Finn', 'Giselle', 'Holden', 'Ivy', 'Jaxon',
        'Kelsey', 'Landon', 'Maddie', 'Nico', 'Peyton', 'Reese', 'Sawyer', 'Talia', 'Vera', 'Weston', 'Xavier', 'Yvette', 'Zara', 'Avery', 'Bennett', 'Cora', 'Declan', 'Eleanor', 'Flynn', 'Gemma', 'Hudson', 'Isabel', 'Jude', 'Kira',
        'Lila', 'Max', 'Nicolette', 'Piper', 'Reid', 'Sienna', 'Tate', 'Vivian', 'Walker', 'Xander', 'Yara', 'Zane', 'Aiden', 'Blair', 'Caden', 'Daphne', 'Elias', 'Felicity', 'Graham', 'Harper', 'Indigo', 'Jace', 'Kendall', 'Liam',
        'Maya', 'Nolan', 'Parker', 'Rory', 'Stella', 'Toby', 'Violet', 'Wren', 'Ximena', 'Yosef', 'Zara'
    ];
    profile_pics TEXT[] := '{}';
    user_id INT;
BEGIN
    FOR i IN 1..100 LOOP
        profile_pics := array_append(profile_pics, 'https://randomuser.me/api/portraits/women/' || i || '.jpg');
        profile_pics := array_append(profile_pics, 'https://randomuser.me/api/portraits/men/' || i || '.jpg');
    END LOOP;

    -- Print the arrays to verify
    RAISE NOTICE 'Names: %', array_to_string(names, ', ');
    RAISE NOTICE 'Profile Pics: %', array_to_string(profile_pics, ', ');

    -- Create a temporary table to store user IDs and profile pictures
    CREATE TEMP TABLE temp_profiles (user_id INT, profile_pic TEXT);

    FOR i IN 1..user_count LOOP
        INSERT INTO users (name, email, password, created_at, remember_token) VALUES
        (names[(floor(random() * array_length(names, 1) + 1))::int] || i, 'user' || i || '@example.com', '$2a$12$GTh8KaAVjeE7PHqShhxDVOChE35KCkT4mTibgiworXDjAUc3rRLKi', CURRENT_TIMESTAMP, NULL)
        RETURNING id INTO user_id;
        -- Password: userpassword

        -- Insert into temporary table
        INSERT INTO temp_profiles (user_id, profile_pic) VALUES
        (user_id, profile_pics[(floor(random() * array_length(profile_pics, 1) + 1))::int]);
    END LOOP;

    -- Update profile table with profile pics using a join with the temporary table
    UPDATE profile
    SET profile_pic = temp_profiles.profile_pic
    FROM temp_profiles
    WHERE profile.id = temp_profiles.user_id;

    -- Drop the temporary table
    DROP TABLE temp_profiles;
END $$;

-- Groups
DO $$
DECLARE
    group_count INT := 10; -- Number of groups to create
    i INT;
BEGIN
    FOR i IN 1..group_count LOOP
        INSERT INTO "group" (name, description, author_id, created_at) VALUES
        ('Group' || i, 'Description for group ' || i, (SELECT id FROM profile WHERE id NOT IN (1, 2) ORDER BY random() LIMIT 1), CURRENT_TIMESTAMP);
    END LOOP;
END $$;

-- Posts and Shares
DO $$
DECLARE
    post_count INT := 500; -- Number of posts to create
    i INT;
    post_contents TEXT[] := ARRAY[
        'Just had a great day!',
        'Loving the new features of this app.',
        'Anyone up for a meetup this weekend?',
        'Check out my new blog post!',
        'Feeling blessed.',
        'Had an amazing dinner at a new restaurant.',
        'Excited for the upcoming holidays!',
        'Just finished a great book.',
        'Looking forward to the weekend.',
        'Feeling productive today!',
        'Just got back from a long trip.',
        'Enjoying the sunny weather.',
        'Feeling inspired.',
        'Just finished a new project.',
        'Had a great workout today.',
        'Feeling motivated.',
        'Just watched a great movie.',
        'Excited for the new year.',
        'Feeling creative today.',
        'Just finished a new painting.'
        'Feeling grateful.',
        'Just finished a new song.',
        'Had a great time at the concert.',
        'Feeling relaxed today.',
        'Just finished a new recipe.',
        'Feeling accomplished.',
        'Just finished a new game.',
        'Had a great time at the party.',
        'Feeling refreshed today.',
        'Just finished a new workout routine.',
        'Feeling positive.',
        'I love this app!',
        'Can''t wait to see what''s next.',
        'Feeling happy today.'
    ];
    share_chance INT;
    post_time TIMESTAMP;
    share_time TIMESTAMP;
    image_urls TEXT[] := '{}';
    image_url TEXT;
BEGIN
    FOR i IN 1..100 LOOP
        image_urls := array_append(image_urls, 'https://picsum.photos/400/300?random=' || i);
        image_urls := array_append(image_urls, 'https://picsum.photos/400/300?random=' || (i + 1000));
    END LOOP;

    -- Print the array to verify
    RAISE NOTICE 'Image URLs: %', array_to_string(image_urls, ', ');

    FOR i IN 1..post_count LOOP
        -- Generate a random timestamp for the post between early October 2024 and December 09, 2024
        post_time := '2024-10-01 00:00:00'::timestamp + (random() * (('2024-12-09 23:59:59'::timestamp - '2024-10-01 00:00:00'::timestamp)))::interval;

        -- Randomly assign an image URL to approximately two-thirds of the posts
        IF floor(random() * 3) < 2 THEN
            image_url := image_urls[(floor(random() * array_length(image_urls, 1) + 1))::int];
        ELSE
            image_url := NULL;
        END IF;

        INSERT INTO post (author_id, group_id, content, image_url, created_at, updated_at) VALUES
        (
            (SELECT id FROM profile ORDER BY random() LIMIT 1),
            CASE
                WHEN random() < 0.8 THEN NULL
                ELSE (SELECT id FROM "group" ORDER BY random() LIMIT 1)
            END,
            post_contents[(floor(random() * array_length(post_contents, 1) + 1))::int],
            image_url,
            post_time,
            post_time
        );

        -- Random chance to create a share (1 in 4 to 1 in 6)
        share_chance := floor(random() * 5) + 1;
        IF share_chance <= 1 THEN
            -- Generate a random timestamp for the share that is later than the post timestamp
            share_time := post_time + (random() * (('2024-12-09 23:59:59'::timestamp - post_time)))::interval;
            INSERT INTO share (author_id, post_id, created_at) VALUES
            ((SELECT id FROM profile ORDER BY random() LIMIT 1), (SELECT id FROM post ORDER BY random() LIMIT 1), share_time);
        END IF;
    END LOOP;
END $$;

-- Comments
DO $$
DECLARE
    comment_count INT := 1000; -- Number of comments to create
    i INT;
    comment_contents TEXT[] := ARRAY[
        'Great post!',
        'I totally agree with you.',
        'Thanks for sharing!',
        'Interesting perspective.',
        'Well said!',
        'I had a similar experience.',
        'This is very helpful.',
        'I appreciate your insight.',
        'Thanks for the update!',
        'Looking forward to more posts like this.'
    ];
BEGIN
    FOR i IN 1..comment_count LOOP
        INSERT INTO comment (author_id, post_id, content, created_at, updated_at) VALUES
        ((SELECT id FROM profile ORDER BY random() LIMIT 1), (SELECT id FROM post ORDER BY random() LIMIT 1), comment_contents[(floor(random() * array_length(comment_contents, 1) + 1))::int], CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
    END LOOP;
END $$;

-- Friendships
DO $$
DECLARE
    friendship_count INT := 500; -- Number of friendships to create
    i INT;
    user_id_var INT;
    friend_id_var INT;
BEGIN
    FOR i IN 1..friendship_count LOOP
        user_id_var := (SELECT id FROM profile WHERE id NOT IN (1, 2) ORDER BY random() LIMIT 1);
        friend_id_var := (SELECT id FROM profile WHERE id NOT IN (1, 2) ORDER BY random() LIMIT 1);
        
        IF user_id_var != friend_id_var THEN
            IF NOT EXISTS (
                SELECT 1 FROM user_friends uf
                WHERE (uf.user_id = user_id_var AND uf.friend_id = friend_id_var) 
                   OR (uf.user_id = friend_id_var AND uf.friend_id = user_id_var)
            ) THEN
                INSERT INTO user_friends (user_id, friend_id) VALUES (user_id_var, friend_id_var)
                ON CONFLICT DO NOTHING;
            END IF;
        END IF;
    END LOOP;
END $$;

-- Blocked users
DO $$
DECLARE
    blocked_count INT := 100; -- Number of blocked users to create
    i INT;
    user_id INT;
    blocked_user_id INT;
BEGIN
    FOR i IN 1..blocked_count LOOP
        user_id := (SELECT id FROM profile ORDER BY random() LIMIT 1);
        blocked_user_id := (SELECT id FROM profile WHERE id NOT IN (1, 2) ORDER BY random() LIMIT 1);
        
        IF user_id != blocked_user_id AND user_id NOT IN (1, 2) THEN
            INSERT INTO user_blocked (user_id, blocked_user_id) VALUES (user_id, blocked_user_id)
            ON CONFLICT DO NOTHING;
        END IF;
    END LOOP;
END $$;

-- Likes on posts
DO $$
DECLARE
    like_count INT := 1000; -- Number of likes to create
    i INT;
BEGIN
    FOR i IN 1..like_count LOOP
        INSERT INTO user_likes (user_id, post_id) VALUES
        ((SELECT id FROM profile ORDER BY random() LIMIT 1), (SELECT id FROM post ORDER BY random() LIMIT 1))
        ON CONFLICT DO NOTHING;
    END LOOP;
END $$;

-- Likes on comments
DO $$
DECLARE
    like_comment_count INT := 500; -- Number of likes on comments to create
    i INT;
BEGIN
    FOR i IN 1..like_comment_count LOOP
        INSERT INTO user_likes_comments (user_id, comment_id) VALUES
        ((SELECT id FROM profile ORDER BY random() LIMIT 1), (SELECT id FROM comment ORDER BY random() LIMIT 1))
        ON CONFLICT DO NOTHING;
    END LOOP;
END $$;

-- Group memberships
DO $$
DECLARE
    group_user_count INT := 500; -- Number of group memberships to create
    i INT;
BEGIN
    FOR i IN 1..group_user_count LOOP
        INSERT INTO group_users (user_id, group_id) VALUES
        (
            (SELECT id FROM profile ORDER BY random() LIMIT 1), 
            (SELECT id FROM "group" WHERE id NOT IN (1, 2) ORDER BY random() LIMIT 1)
        )
        ON CONFLICT DO NOTHING;
    END LOOP;
END $$;

-- Friend request notifications
DO $$
DECLARE
    i INT;
    sender_id INT;
    notif_id INT;
    friendship_exists BOOLEAN;
BEGIN
    FOR i IN 30..59 LOOP
        sender_id := i;

        -- Check if friendship already exists
        SELECT EXISTS (
            SELECT 1
            FROM user_friends
            WHERE (user_id = 3 AND friend_id = sender_id) OR (user_id = sender_id AND friend_id = 3)
        ) INTO friendship_exists;

        -- If no friendship exists, send friend request
        IF NOT friendship_exists THEN
            INSERT INTO notif (receiver_id, sender_id, created_at, notification_type) 
            VALUES (3, sender_id, CURRENT_TIMESTAMP, 'friend_request')
            RETURNING id INTO notif_id;

            INSERT INTO friend_request_notification (notification_id, request_status) 
            VALUES (notif_id, 'pending');
        END IF;
    END LOOP;
END $$;


-- Group invite notifications
DO $$
DECLARE
    group_invite_count INT := 100; -- Number of group invite notifications to create
    i INT;
BEGIN
    FOR i IN 1..group_invite_count LOOP
        INSERT INTO group_invite_notification (notification_id, group_id, request_status) VALUES
        ((SELECT id FROM notif ORDER BY random() LIMIT 1), (SELECT id FROM "group" ORDER BY random() LIMIT 1), 'pending');
    END LOOP;
END $$;

-- Post like notifications
DO $$
DECLARE
    post_like_notif_count INT := 100; -- Number of post like notifications to create
    i INT;
BEGIN
    FOR i IN 1..post_like_notif_count LOOP
        INSERT INTO post_like_notification (notification_id, post_id) VALUES
        ((SELECT id FROM notif ORDER BY random() LIMIT 1), (SELECT id FROM post ORDER BY random() LIMIT 1));
    END LOOP;
END $$;

-- Comment like notifications
DO $$
DECLARE
    comment_like_notif_count INT := 100; -- Number of comment like notifications to create
    i INT;
BEGIN
    FOR i IN 1..comment_like_notif_count LOOP
        INSERT INTO comment_like_notification (notification_id, comment_id) VALUES
        ((SELECT id FROM notif ORDER BY random() LIMIT 1), (SELECT id FROM comment ORDER BY random() LIMIT 1));
    END LOOP;
END $$;

-- Post comment notifications
DO $$
DECLARE
    post_comment_notif_count INT := 100; -- Number of post comment notifications to create
    i INT;
BEGIN
    FOR i IN 1..post_comment_notif_count LOOP
        INSERT INTO post_comment_notification (notification_id, comment_id, post_id) VALUES
        ((SELECT id FROM notif ORDER BY random() LIMIT 1), (SELECT id FROM comment ORDER BY random() LIMIT 1), (SELECT id FROM post ORDER BY random() LIMIT 1));
    END LOOP;
END $$;

-- Post share notifications
DO $$
DECLARE
    post_share_notif_count INT := 100; -- Number of post share notifications to create
    i INT;
BEGIN
    FOR i IN 1..post_share_notif_count LOOP
        INSERT INTO post_share_notification (notification_id, post_id, share_id) VALUES
        ((SELECT id FROM notif ORDER BY random() LIMIT 1), (SELECT id FROM post ORDER BY random() LIMIT 1), (SELECT id FROM share ORDER BY random() LIMIT 1));
    END LOOP;
END $$;
-----------------------------------------
-- end
-----------------------------------------