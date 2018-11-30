<?php

namespace AnyComment\Providers;


use Hybridauth\Adapter\OAuth2;
use Hybridauth\Data\Collection;
use Hybridauth\Exception\Exception;
use Hybridauth\Exception\UnexpectedApiResponseException;
use Hybridauth\User\Profile;

/**
 * Class Yandex is a provider for Hybriauth library.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Providers
 */
class Yandex extends OAuth2 {
	/**
	 * {@inheritdoc}
	 */
	protected $apiBaseUrl = 'https://login.yandex.ru/info';

	/**
	 * {@inheritdoc}
	 */
	protected $authorizeUrl = 'https://oauth.yandex.ru/authorize';

	/**
	 * {@inheritdoc}
	 */
	protected $accessTokenUrl = 'https://oauth.yandex.ru/token';

	/**
	 * {@inheritdoc}
	 */
	public function hasAccessTokenExpired() {
		// As we using offline scope, $expired will be false.
		$expired = $this->getStoredData( 'expires_in' )
			? $this->getStoredData( 'expires_at' ) <= time()
			: false;

		return $expired;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function validateAccessTokenExchange( $response ) {
		$data = parent::validateAccessTokenExchange( $response );

		// Need to store user_id as token for later use.
		$this->storeData( 'user_id', $data->get( 'user_id' ) );
		$this->storeData( 'email', $data->get( 'email' ) );
	}

	/**
	 * load the user profile from the IDp api client
	 *
	 * @throws Exception
	 */
	public function getUserProfile() {

		$this->scope = implode( ',', [] );

		$response = $this->apiRequest( $this->apiBaseUrl . "?format=json", 'GET' );

		if ( ! isset( $response->id ) ) {
			throw new UnexpectedApiResponseException( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
		}

		$data = new Collection( $response );

		if ( ! $data->exists( 'id' ) ) {
			throw new UnexpectedApiResponseException( 'Provider API returned an unexpected response.' );
		}

		$userProfile = new Profile();

		$userProfile->identifier    = $data->get( 'id' );
		$userProfile->firstName     = $data->get( 'real_name' );
		$userProfile->lastName      = $data->get( 'family_name' );
		$userProfile->displayName   = $data->get( 'display_name' );
		$userProfile->photoURL      = 'http://upics.yandex.net/' . $userProfile->identifier . '/normal';
		$userProfile->profileURL    = "";
		$userProfile->gender        = $data->get( 'sex' );
		$userProfile->email         = $data->get( 'default_email' );
		$userProfile->emailVerified = $data->get( 'default_email' );

		if ( $data->get( 'birthday' ) ) {
			list( $birthday_year, $birthday_month, $birthday_day ) = explode( '-', $response->birthday );
			$userProfile->birthDay   = (int) $birthday_day;
			$userProfile->birthMonth = (int) $birthday_month;
			$userProfile->birthYear  = (int) $birthday_year;
		}

		return $userProfile;
	}
}