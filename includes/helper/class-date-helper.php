<?php
/** @noinspection PhpUnused */

declare( strict_types=1 );

namespace MNCA_WP\Helper;

use DateInterval;
use DatePeriod;
use DateTime;
use IntlDateFormatter;

/**
 * @since 1.0.0
 * @author MNCA
 * @package MNCA_WP
 * @subpackage MNCA_WP\Helper
 */
class Date_Helper {

	/**
	 * Return an i18n main formatter for all dates and times
	 *
	 * @see MNCA_Agenda_WP_Helper::get_the_formatted_time()
	 * @see MNCA_Agenda_WP_Helper::get_the_formatted_date()
	 *
	 * @return IntlDateFormatter
	 */
	public static function get_datetime_formatter(): IntlDateFormatter {
		return new IntlDateFormatter(
			get_locale(),
			IntlDateFormatter::NONE,
			IntlDateFormatter::NONE,
			wp_timezone(),
			IntlDateFormatter::GREGORIAN
		);
	}

	/**
	 * Format a date
	 *
	 * @see MNCA_Agenda_WP_Helper::get_datetime_formatter()
	 *
	 * @param string $input_format Input format of $date
	 *
	 * @param string $date Date with format Y-m-d
	 *
	 * @return string The formatted date
	 * @todo Verify the string format of $date checking DateTime::createFromFormat result throwing a custom Exception
	 *
	 */
	public static function get_the_formatted_date( string $date, string $input_format = 'Ymd', string $output_format = 'EEEE d MMMM Y' ): string {
		$date_formatter = self::get_datetime_formatter();
		$date_formatter->setPattern( $output_format );

		$date = DateTime::createFromFormat( $input_format, $date, wp_timezone() );

		return $date_formatter->format( $date );
	}

	/**
	 * Format a time
	 *
	 * @see MNCA_Agenda_WP_Helper::get_datetime_formatter()
	 *
	 * @param string $input_format Input format of $time
	 *
	 * @param string $time Time with format H:i:s
	 *
	 * @return string The formatted time
	 * @todo Verify the string format H:i:s of $time checking DateTime::createFromFormat result throwing a custom Exception
	 *
	 */
	public static function get_the_formatted_time( string $time, string $input_format = 'H:i:s' ): string {
		$time_formatter = self::get_datetime_formatter();
		$locale         = get_locale();

		$time = DateTime::createFromFormat( $input_format, $time, wp_timezone() );

		if ( strpos( $locale, 'fr' ) !== false ) {
			/** @noinspection SpellCheckingInspection */
			$time_formatter->setPattern( "HH'h'mm" );
		} else {
			$time_formatter->setPattern( "HH:mm" );
		}

		return $time_formatter->format( $time );
	}

	/**
	 * Format a datetime in ISO 8601 format for microdata
	 *
	 * @param string $date Date with $input_date_format string format
	 * @param string $time Time with $input_date_time string format.
	 * @param string $input_date_format String Format of $date. Default is Y-m-d.
	 * @param string $input_time_format String Format of $time. Default is H:i:s.
	 *
	 * @return string The datetime formatted in ISO 8601 format
	 */
	public static function convert_datetime_iso8601( string $date, string $time, string $input_date_format = 'Y-m-d', string $input_time_format = 'H:i:s' ): string {
		$datetime = DateTime::createFromFormat(
			$input_date_format . ' ' . $input_time_format,
			$date . ' ' . $time,
			wp_timezone()
		);

		return $datetime->format( 'c' );
	}

	/**
	 * Format a date in ISO 8601 format for microdata
	 *
	 * @param string $date Date with $input_date_format string format
	 * @param string $input_date_format String Format of $date. Default is Y-m-d.
	 *
	 * @return string The date formatted in ISO 8601 format
	 */
	public static function convert_date_iso8601( string $date, string $input_date_format = 'Y-m-d' ): string {
		$datetime = DateTime::createFromFormat(
			$input_date_format,
			$date,
			wp_timezone()
		);

		return $datetime->format( 'Y-m-d' );
	}

	/**
	 * Convert a date in ISO 8601 format to list of exploded strings.
	 *
	 * @throws \Exception
	 *
	 * @param string $datetime_iso8601 The date to parse in ISO 8601 format
	 *
	 * @return array{
	 *     weekday: string,
	 *     short_weekday: string,
	 *     day: string,
	 *     month: string,
	 *     short_month: string,
	 *     year: string,
	 *     hour: string,
	 *     minute: string,
	 *     second: string} The array of parsed date.
	 */
	public static function convert_datetime_iso8601_parsed( string $datetime_iso8601 ): array {

		$datetime = new DateTime( $datetime_iso8601 );

		$date_formatter = Date_Helper::get_datetime_formatter();
		$date_formatter->setPattern( 'MMMM' );

		$month_i18n = $date_formatter->format( $datetime );

		$date_formatter->setPattern( 'MMM' );
		$short_month_i18n = $date_formatter->format( $datetime );

		$date_formatter->setPattern( 'EEEE' );
		$weekday_i18n = $date_formatter->format( $datetime );

		$date_formatter->setPattern( 'EEE' );
		$short_weekday_i18n = $date_formatter->format( $datetime );


		return array(
			'weekday'       => $weekday_i18n,
			'short_weekday' => $short_weekday_i18n,
			'day'           => $datetime->format( 'd' ),
			'month'         => $month_i18n,
			'short_month'   => $short_month_i18n,
			'year'          => $datetime->format( 'Y' ),
			'hour'          => $datetime->format( 'H' ),
			'minute'        => $datetime->format( 'i' ),
			'second'        => $datetime->format( 's' )
		);
	}

	/**
	 * Retrieve all dates between two dates
	 *
	 * @throws \Exception
	 *
	 * @param string $end The end date of the range in Y-m-d format
	 * @param string $format The format of the dates returned
	 *
	 * @param string $start The start date of the range in Y-m-d format
	 *
	 * @return array An array of formatted dates available in the range
	 */
	public static function get_dates_from_range( string $start, string $end, string $format = 'Ymd' ): array {

		$dates_array = array();
		$interval    = new DateInterval( 'P1D' );
		$real_end    = new DateTime( $end );

		$real_end->add( $interval );

		$period = new DatePeriod( new DateTime( $start ), $interval, $real_end );

		foreach ( $period as $date ) {
			$dates_array[] = $date->format( $format );
		}

		return $dates_array;
	}
}
