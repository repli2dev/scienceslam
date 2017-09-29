ALTER TABLE page ADD COLUMN gallery_meta TINYINT(1) DEFAULT 0 AFTER gallery_path;
ALTER TABLE page ADD COLUMN gallery_meta_title TEXT NULL AFTER gallery_path;
ALTER TABLE page ADD COLUMN gallery_meta_weight TINYINT(1) DEFAULT 0 AFTER gallery_meta_title;
ALTER TABLE page ADD COLUMN is_meta_gallery TINYINT(1) DEFAULT 0 AFTER gallery_meta_weight;
