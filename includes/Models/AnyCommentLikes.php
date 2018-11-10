<?php

namespace AnyComment\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_Comment;

use AnyComment\Helpers\AnyCommentRequest;

/**
 * Class AnyCommentLikes.
 *
 * @property int $ID
 * @property int|null $user_ID
 * @property int $comment_ID
 * @property int $post_ID
 * @property string $user_agent
 * @property string $ip
 * @property string $liked_at
 *
 * @since 0.0.3
 */
class AnyCommentLikes extends AnyCommentActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static $table_name = 'likes';

	public $ID;
	public $user_ID;
	public $comment_ID;
	public $post_ID;
	public $user_agent;
	public $ip;
	public $liked_at;

	/**
	 * @param $commentId
	 *
	 * @return bool|int
	 */
	public static function is_current_user_has_like( $commentId, $userId = null ) {
		if ( ! ( $comment = get_comment( $commentId ) ) instanceof WP_Comment ) {
			return false;
		}

		if ( $userId === null && ! (int) ( $userId = get_current_user_id() ) === 0 ) {
			return false;
		}

		if ( $userId !== null && ! get_user_by( 'id', $userId ) ) {
			return false;
		}

		global $wpdb;


		$tableName = static::get_table_name();

		$sql   = $wpdb->prepare( "SELECT COUNT(*) FROM $tableName WHERE `user_ID` =%d AND `comment_ID`=%s", $userId, $comment->comment_ID );
		$count = $wpdb->get_var( $sql );

		return $count >= 1;
	}

	/**
	 * Get likes count per comment.
	 *
	 * @param int $userId User ID to search for.
	 *
	 * @return int
	 */
	public static function get_likes_count_by_user( $userId ) {
		global $wpdb;

		$table_name = static::get_table_name();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `user_ID`=%d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $userId ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count;
	}


	/**
	 * Get likes count per comment.
	 *
	 * @param int $commentId Comment ID to be searched for.
	 *
	 * @return int
	 */
	public static function get_likes_count( $commentId ) {
		global $wpdb;

		$table_name = static::get_table_name();
		$sql        = "SELECT COUNT(*) FROM $table_name WHERE `comment_ID`=%d";
		$count      = $wpdb->get_var( $wpdb->prepare( $sql, [ $commentId ] ) );

		if ( $count === null ) {
			return 0;
		}

		return (int) $count;
	}

	/**
	 * Delete all like by provided comment ID.
	 *
	 * @since 0.0.3
	 *
	 * @param int $commentId ID of the comment to delete like from.
	 *
	 * @return bool
	 */
	public static function deleteLikes( $commentId ) {
		if ( empty( $commentId ) ) {
			return false;
		}

		$userId = get_current_user_id();

		if ( (int) $userId === 0 ) {
			return false;
		}

		global $wpdb;

		$rows = $wpdb->delete( static::get_table_name(), [ 'comment_ID' => $commentId ] );

		return $rows !== false && $rows >= 0;
	}


	/**
	 * Delete single like.
	 *
	 * @since 0.0.3
	 *
	 * @param int $commentId ID of the comment to delete like from.
	 *
	 * @return bool
	 */
	public static function delete_like( $commentId ) {
		if ( empty( $commentId ) ) {
			return false;
		}

		$userId = get_current_user_id();

		if ( (int) $userId === 0 ) {
			return false;
		}

		global $wpdb;

		$rows = $wpdb->delete( static::get_table_name(), [ 'user_ID' => $userId, 'comment_ID' => $commentId ] );

		return $rows !== false && $rows >= 0;
	}

	/**
	 * Inserts a comment into the database.
	 *
	 * @since 0.0.3
	 *
	 * @return bool
	 */
	public function save() {
		if ( ! isset( $this->ip ) ) {
			$this->ip = AnyCommentRequest::get_user_ip();
		}

		if ( ! isset( $this->liked_at ) ) {
			$this->liked_at = current_time( 'mysql' );
		}

		global $wpdb;

		$tableName = static::get_table_name();

		unset( $this->ID );

		$count = $wpdb->insert( $tableName, (array) $this );


		if ( $count !== false && $count > 0 ) {
			$lastId = $wpdb->insert_id;

			if ( empty( $lastId ) ) {
				return false;
			}

			$this->ID = $lastId;

			return true;
		}

		return false;
	}
}