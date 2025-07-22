-- Acumen Craft – სრული მონაცემთა მოდელი (SQL, განახლებული 2025-07-22)

-- USERS
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255),
  provider VARCHAR(32),
  provider_id VARCHAR(128),
  oauth_avatar VARCHAR(512),
  oauth_email_verified BOOLEAN DEFAULT 0,
  twofa_secret VARCHAR(255),
  avatar_path VARCHAR(255),
  bio TEXT,
  creative_field VARCHAR(128),
  lang VARCHAR(8) DEFAULT 'en',
  notification_prefs JSON,
  privacy_prefs JSON,
  role ENUM('user','artist','moderator','admin') DEFAULT 'user',
  status ENUM('active','suspended','deleted') DEFAULT 'active',
  email_verified_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- LINKED ACCOUNTS (OAuth)
CREATE TABLE linked_accounts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  provider VARCHAR(32) NOT NULL,
  provider_id VARCHAR(128) NOT NULL,
  email VARCHAR(255),
  avatar_url VARCHAR(512),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ARTWORKS
CREATE TABLE artworks (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  title_translations JSON NOT NULL,
  description_translations JSON,
  media_type ENUM('image','audio','video','pdf') NOT NULL,
  media_path VARCHAR(255) NOT NULL,
  status ENUM('pending','approved','rejected','private') DEFAULT 'pending',
  tags JSON,
  categories JSON,
  copyright_notice TEXT,
  license_type ENUM('all_rights_reserved','cc_by','cc_by_sa','cc_by_nc','cc_by_nd','nft','ai_generated') DEFAULT 'all_rights_reserved',
  license_detail TEXT,
  file_hash VARCHAR(128),
  upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  watermark_meta JSON,
  visibility ENUM('public','private','unlisted') DEFAULT 'public',
  blockchain_txid VARCHAR(128),
  ai_generated BOOLEAN DEFAULT 0,
  ai_edit_detail TEXT,
  acq_score DECIMAL(4,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- EVALUATIONS
CREATE TABLE evaluations (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  artwork_id BIGINT UNSIGNED NOT NULL,
  evaluator_id BIGINT UNSIGNED,
  score_technique SMALLINT,
  score_composition SMALLINT,
  score_originality SMALLINT,
  score_impact SMALLINT,
  feedback_text TEXT,
  source ENUM('human','ai','aggregate') DEFAULT 'human',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (artwork_id) REFERENCES artworks(id),
  FOREIGN KEY (evaluator_id) REFERENCES users(id)
);

-- PAYMENTS
CREATE TABLE payments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(4) NOT NULL,
  provider ENUM('stripe','paypal') NOT NULL,
  status ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  payment_id VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  refunded_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- WITHDRAWALS
CREATE TABLE withdrawals (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(4) NOT NULL,
  provider ENUM('stripe','paypal') NOT NULL,
  status ENUM('pending','processing','completed','failed') DEFAULT 'pending',
  requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  processed_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- CRYPTO_PAYMENTS
CREATE TABLE crypto_payments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(12,6) NOT NULL,
  currency VARCHAR(8) NOT NULL,
  tx_hash VARCHAR(128),
  status ENUM('pending','confirmed','failed') DEFAULT 'pending',
  network VARCHAR(32),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- NFT OWNERSHIP
CREATE TABLE nft_ownership (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  artwork_id BIGINT UNSIGNED NOT NULL,
  owner_wallet VARCHAR(128) NOT NULL,
  network VARCHAR(32),
  token_id VARCHAR(128),
  tx_hash VARCHAR(128),
  minted_at TIMESTAMP,
  FOREIGN KEY (artwork_id) REFERENCES artworks(id)
);

-- COMMUNITIES (Groups)
CREATE TABLE communities (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name_translations JSON NOT NULL,
  description_translations JSON,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE community_members (
  community_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  role ENUM('admin','moderator','member') DEFAULT 'member',
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (community_id, user_id),
  FOREIGN KEY (community_id) REFERENCES communities(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE community_posts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  community_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  content_translations JSON,
  media_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (community_id) REFERENCES communities(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE community_post_comments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  post_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  content_translations JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES community_posts(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- CONVERSATIONS & MESSAGES
CREATE TABLE conversations (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  type ENUM('direct','group','ai') DEFAULT 'direct',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE conversation_participants (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  conversation_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE messages (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  conversation_id BIGINT UNSIGNED NOT NULL,
  sender_id BIGINT UNSIGNED NOT NULL,
  message TEXT,
  message_type ENUM('text','image','file','system','ai') DEFAULT 'text',
  status ENUM('sent','delivered','read') DEFAULT 'sent',
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP,
  attachment_url VARCHAR(255),
  FOREIGN KEY (conversation_id) REFERENCES conversations(id),
  FOREIGN KEY (sender_id) REFERENCES users(id)
);

-- AI CHAT
CREATE TABLE ai_chat_sessions (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED,
  guest_id VARCHAR(64),
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ended_at TIMESTAMP
);

CREATE TABLE ai_chat_messages (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  session_id BIGINT UNSIGNED NOT NULL,
  role ENUM('user','assistant','system') DEFAULT 'user',
  content TEXT,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (session_id) REFERENCES ai_chat_sessions(id)
);

-- HELP ARTICLES & SUPPORT TICKETS
CREATE TABLE help_articles (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  category VARCHAR(64),
  title_translations JSON NOT NULL,
  content_translations JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE support_tickets (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  subject VARCHAR(255),
  description TEXT,
  status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
  assigned_to BIGINT UNSIGNED,
  priority ENUM('low','normal','high','urgent') DEFAULT 'normal',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resolved_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE ticket_messages (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  ticket_id BIGINT UNSIGNED NOT NULL,
  sender_id BIGINT UNSIGNED NOT NULL,
  content TEXT,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES support_tickets(id),
  FOREIGN KEY (sender_id) REFERENCES users(id)
);

-- NOTIFICATIONS
CREATE TABLE notifications (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(64),
  content JSON,
  data JSON,
  read_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE notification_settings (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(64),
  enabled BOOLEAN DEFAULT 1,
  channel ENUM('in_app','email','push'),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- SECURITY LOGS
CREATE TABLE security_logs (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED,
  action VARCHAR(255),
  ip VARCHAR(64),
  meta TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- API CLIENTS & USAGE
CREATE TABLE api_clients (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255),
  api_key VARCHAR(128),
  scope VARCHAR(255),
  enabled BOOLEAN DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE api_usage (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  client_id BIGINT UNSIGNED NOT NULL,
  endpoint VARCHAR(255),
  count INT,
  day DATE,
  FOREIGN KEY (client_id) REFERENCES api_clients(id)
);

-- ANALYTICS
CREATE TABLE analytics_events (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED,
  event_type VARCHAR(64),
  event_data JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE admin_reports (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  report_type VARCHAR(64),
  data JSON,
  generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INFRINGEMENT REPORTS (Copyright)
CREATE TABLE infringement_reports (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  artwork_id BIGINT UNSIGNED,
  reporter_id BIGINT UNSIGNED NULL,
  reporter_contact VARCHAR(255),
  description TEXT,
  assertion TEXT,
  evidence TEXT,
  status ENUM('pending','reviewing','resolved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  reviewed_at TIMESTAMP NULL,
  FOREIGN KEY (artwork_id) REFERENCES artworks(id),
  FOREIGN KEY (reporter_id) REFERENCES users(id)
);

-- INDEXES & PERFORMANCE OPTIMIZATION
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_artworks_user_id ON artworks(user_id);
CREATE INDEX idx_payments_user_id ON payments(user_id);
CREATE INDEX idx_messages_conversation_id ON messages(conversation_id);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
