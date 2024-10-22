-- Table: users
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role INT NOT NULL,
  profile_pic VARCHAR(255),
  motd VARCHAR(255),
  badge_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: badges
CREATE TABLE badges (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NOT NULL,
  icon VARCHAR(255) NOT NULL,
  game_id INT
);

-- Adding foreign key to `users` referencing `badges`
ALTER TABLE users ADD CONSTRAINT fk_badge_id FOREIGN KEY (badge_id) REFERENCES badges(id);

-- Table: game
CREATE TABLE game (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  title VARCHAR(255) NOT NULL,
  short_description VARCHAR(255),
  description TEXT,
  launch_date TIMESTAMP,
  base_price FLOAT,
  discount FLOAT,
  file VARCHAR(255),
  version VARCHAR(50),
  developer_id INT
);

-- Table: developers
CREATE TABLE developers (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name VARCHAR(255) NOT NULL,
  profile_pic VARCHAR(255),
  motd VARCHAR(255),
  owner_id INT NOT NULL,
  FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Adding foreign key to `game` referencing `developers`
ALTER TABLE game ADD CONSTRAINT fk_developer_id FOREIGN KEY (developer_id) REFERENCES developers(id);

-- Table: owns
CREATE TABLE owns (
  user_id INT NOT NULL,
  game_id INT NOT NULL,
  base_price FLOAT NOT NULL,
  discount FLOAT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (game_id) REFERENCES game(id)
);

-- Table: posts
CREATE TABLE posts (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  title VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_updated_at TIMESTAMP,
  is_public BOOLEAN DEFAULT FALSE,
  type INT,
  game_id INT,
  author_id INT,
  FOREIGN KEY (game_id) REFERENCES game(id),
  FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Table: gallery_entries
CREATE TABLE gallery_entries (
  post_id INT PRIMARY KEY NOT NULL,
  media VARCHAR(255) NOT NULL,
  FOREIGN KEY (post_id) REFERENCES posts(id)
);

-- Table: guide_types
CREATE TABLE guide_types (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  icon VARCHAR(255) NOT NULL,
  type VARCHAR(100) NOT NULL
);

-- Table: guides
CREATE TABLE guides (
  post_id INT PRIMARY KEY NOT NULL,
  type_id INT NOT NULL,
  FOREIGN KEY (post_id) REFERENCES posts(id),
  FOREIGN KEY (type_id) REFERENCES guide_types(id)
);

-- Table: comments
CREATE TABLE comments (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  author_id INT NOT NULL,
  post_id INT NOT NULL,
  body TEXT NOT NULL,
  FOREIGN KEY (author_id) REFERENCES users(id),
  FOREIGN KEY (post_id) REFERENCES posts(id)
);

-- Table: votes
CREATE TABLE votes (
  user_id INT NOT NULL,
  post_id INT NOT NULL,
  is_downvote BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (post_id) REFERENCES posts(id)
);

-- Adding foreign key to `badges` referencing `game`
ALTER TABLE badges ADD CONSTRAINT fk_game_id FOREIGN KEY (game_id) REFERENCES game(id);

-- Table: badge_unlocked
CREATE TABLE badge_unlocked (
  badge_id INT NOT NULL,
  user_id INT NOT NULL,
  date TIMESTAMP NOT NULL,
  FOREIGN KEY (badge_id) REFERENCES badges(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table: achievements
CREATE TABLE achievements (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NOT NULL,
  icon VARCHAR(255) NOT NULL,
  locked_icon VARCHAR(255),
  secret BOOLEAN DEFAULT FALSE,
  game_id INT,
  FOREIGN KEY (game_id) REFERENCES game(id)
);

-- Table: leaderboards
CREATE TABLE leaderboards (
  id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  concept INT NOT NULL,
  type INT NOT NULL,
  game_id INT,
  FOREIGN KEY (game_id) REFERENCES game(id)
);

-- Table: entries
CREATE TABLE entries (
  leaderboard_id INT NOT NULL,
  user_id INT NOT NULL,
  value INT NOT NULL,
  FOREIGN KEY (leaderboard_id) REFERENCES leaderboards(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table: unlocks
CREATE TABLE unlocks (
  achievement_id INT NOT NULL,
  user_id INT NOT NULL,
  date TIMESTAMP NOT NULL,
  FOREIGN KEY (achievement_id) REFERENCES achievements(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
