ALTER TABLE `prefix_blog` DROP FOREIGN KEY `prefix_blog_ibfk_1` ;
ALTER TABLE `prefix_blog` DROP `parent_id`;
ALTER TABLE `prefix_blog` DROP `order_num`;
ALTER TABLE `prefix_blog` DROP `blogs_only`;
DROP TABLE  `prefix_topic_blog`;