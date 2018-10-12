<?php

class AnyCommentMigration_0_0_32 extends AnyCommentMigration {
	public $version = '0.0.32';

	/**
	 * {@inheritdoc}
	 */
	public function isApplied() {
		global $wpdb;

		$res = $wpdb->get_results( "SHOW TABLES LIKE 'anycomment_likes';", 'ARRAY_A' );

		if ( ! empty( $res ) && count( $res ) == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up() {
		global $wpdb;

		$table = 'anycomment_likes';

		$sql = "CREATE TABLE `$table` (
  `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `post_ID` bigint(20) UNSIGNED NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `liked_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		if ( $wpdb->query( $sql ) !== false ) {

			$user_id_index = "ALTER TABLE `$table` ADD INDEX `user_ID` (`user_ID`)";
			$post_id_index = "ALTER TABLE `$table` ADD INDEX `post_ID` (`post_ID`)";

			$wpdb->query( $user_id_index );
			$wpdb->query( $post_id_index );
		}


		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function down() {
		global $wpdb;

		$sql = "DROP TABLE IF EXISTS `anycomment_likes`;";
		$wpdb->query( $sql );

		return true;
	}
}