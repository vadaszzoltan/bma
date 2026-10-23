<?php
/**
 * Branded HTML layout for every outgoing email.
 *
 * Plain-text messages (the plugin's editable templates, the contact form and
 * WordPress core mails) are wrapped into one HTML layout; the original text is
 * kept as the plain-text alternative.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Email_Layout {

	/**
	 * Plain-text version of the message being sent, used as AltBody.
	 *
	 * @var string|null
	 */
	private static ?string $alt_body = null;

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_filter( 'wp_mail', array( self::class, 'wrap' ), 20 );
		add_action( 'phpmailer_init', array( self::class, 'set_alt_body' ), 20 );
	}

	/**
	 * Turn a plain-text wp_mail() call into a branded HTML one.
	 *
	 * @param array<string, mixed> $args wp_mail() arguments.
	 * @return array<string, mixed>
	 */
	public static function wrap( array $args ): array {
		$message = (string) ( $args['message'] ?? '' );
		$headers = $args['headers'] ?? array();
		if ( ! is_array( $headers ) ) {
			$headers = explode( "\n", str_replace( "\r\n", "\n", (string) $headers ) );
		}

		foreach ( $headers as $header ) {
			if ( preg_match( '/^\s*content-type:\s*text\/html/i', (string) $header ) ) {
				return $args;
			}
		}
		if ( '' === trim( $message ) || preg_match( '/<(html|body|table|p|div)\b/i', $message ) ) {
			return $args;
		}

		$headers   = array_values(
			array_filter(
				$headers,
				static fn( $h ) => '' !== trim( (string) $h ) && ! preg_match( '/^\s*content-type:/i', (string) $h )
			)
		);
		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		self::$alt_body   = $message;
		$args['headers']  = $headers;
		$args['message']  = self::render( (string) ( $args['subject'] ?? '' ), $message );

		return $args;
	}

	/**
	 * Keep the original text as the plain-text part.
	 *
	 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Mailer.
	 */
	public static function set_alt_body( $phpmailer ): void {
		if ( null !== self::$alt_body && 'text/html' === $phpmailer->ContentType ) {
			$phpmailer->AltBody = self::$alt_body;
		}
		self::$alt_body = null;
	}

	/**
	 * Render the full HTML document.
	 *
	 * @param string $subject Subject (used as preheader/title).
	 * @param string $text    Plain-text body.
	 */
	public static function render( string $subject, string $text ): string {
		$c       = BMA_AJ_Appearance::get_colors();
		$font    = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
		$lang    = substr( (string) get_locale(), 0, 2 );
		$body    = self::text_to_html( $text, $c );
		$company = BMA_AJ_Company::get( 'company_name' );
		$address = str_replace( "\n", ', ', BMA_AJ_Company::get( 'contact_address' ) );
		$phone   = BMA_AJ_Company::get( 'contact_phone' );
		$email   = BMA_AJ_Company::get( 'contact_email' );
		$home    = home_url( '/' );
		$host    = (string) wp_parse_url( $home, PHP_URL_HOST );

		ob_start();
		?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( $lang ); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light">
<title><?php echo esc_html( $subject ); ?></title>
</head>
<body style="margin:0;padding:0;background:<?php echo esc_attr( $c['bg'] ); ?>;-webkit-text-size-adjust:100%;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;"><?php echo esc_html( $subject ); ?></div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:<?php echo esc_attr( $c['bg'] ); ?>;">
	<tr>
		<td align="center" style="padding:32px 16px;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">
				<tr>
					<td style="background:<?php echo esc_attr( $c['primary'] ); ?>;border-radius:8px 8px 0 0;padding:22px 32px;border-bottom:4px solid <?php echo esc_attr( $c['accent'] ); ?>;">
						<a href="<?php echo esc_url( $home ); ?>" style="font-family:<?php echo esc_attr( $font ); ?>;font-size:20px;font-weight:700;letter-spacing:2px;color:#FFFFFF;text-decoration:none;">BMA <span style="color:<?php echo esc_attr( $c['accent'] ); ?>;">ENGINEERING</span></a>
					</td>
				</tr>
				<tr>
					<td style="background:<?php echo esc_attr( $c['surface'] ); ?>;padding:32px;font-family:<?php echo esc_attr( $font ); ?>;font-size:16px;line-height:1.6;color:<?php echo esc_attr( $c['text'] ); ?>;border-left:1px solid <?php echo esc_attr( $c['border'] ); ?>;border-right:1px solid <?php echo esc_attr( $c['border'] ); ?>;">
						<?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped text in text_to_html(). ?>
					</td>
				</tr>
				<tr>
					<td style="background:<?php echo esc_attr( $c['footer_bg'] ); ?>;border-radius:0 0 8px 8px;padding:20px 32px;font-family:<?php echo esc_attr( $font ); ?>;font-size:13px;line-height:1.6;color:<?php echo esc_attr( $c['footer_text'] ); ?>;">
						<strong style="color:#FFFFFF;"><?php echo esc_html( $company ); ?></strong><br>
						<?php echo esc_html( $address ); ?><br>
						<?php if ( $phone ) : ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" style="color:<?php echo esc_attr( $c['footer_text'] ); ?>;text-decoration:none;"><?php echo esc_html( $phone ); ?></a> &middot;
						<?php endif; ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>" style="color:<?php echo esc_attr( $c['footer_text'] ); ?>;text-decoration:none;"><?php echo esc_html( $email ); ?></a> &middot;
						<a href="<?php echo esc_url( $home ); ?>" style="color:<?php echo esc_attr( $c['accent'] ); ?>;text-decoration:none;"><?php echo esc_html( $host ); ?></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Convert the plain-text body to simple, inline-styled HTML.
	 *
	 * Blank lines separate paragraphs, a line holding only a URL becomes a button,
	 * "- " / "• " lines become a list and short "Label: value" lines get a bold label.
	 *
	 * @param string               $text Plain text.
	 * @param array<string,string> $c    Colors.
	 */
	private static function text_to_html( string $text, array $c ): string {
		$text   = trim( str_replace( "\r\n", "\n", $text ) );
		$blocks = preg_split( "/\n\s*\n/", $text );
		$p      = 'margin:0 0 16px;';
		$link   = 'color:' . $c['primary'] . ';text-decoration:underline;';
		$html   = '';

		foreach ( $blocks as $block ) {
			$lines = array_values( array_filter( array_map( 'rtrim', explode( "\n", $block ) ), static fn( $l ) => '' !== trim( $l ) ) );
			$out   = array();
			$list  = array();

			foreach ( $lines as $line ) {
				$trim = trim( $line );
				// WordPress core wraps some URLs in angle brackets.
				$trim = preg_replace( '/^<(https?:\/\/[^>\s]+)>$/', '$1', $trim );

				if ( preg_match( '/^[-•*]\s+(.+)$/u', $trim, $m ) ) {
					$list[] = self::inline( $m[1], $link );
					continue;
				}
				if ( $list ) {
					$out[] = self::list_html( $list );
					$list  = array();
				}

				if ( preg_match( '/^https?:\/\/\S+$/', $trim ) ) {
					$out[] = '<a href="' . esc_url( $trim ) . '" style="display:inline-block;margin:4px 0;padding:12px 22px;background:' . esc_attr( $c['accent'] ) . ';color:#FFFFFF;font-weight:600;text-decoration:none;border-radius:6px;word-break:break-all;">' . esc_html( self::short_url( $trim ) ) . '</a>';
					continue;
				}

				if ( preg_match( '/^([^:.!?\n]{1,32}):\s+(.+)$/u', $trim, $m ) && ! preg_match( '/^https?$/i', $m[1] ) ) {
					$out[] = '<strong>' . esc_html( $m[1] ) . ':</strong> ' . self::inline( $m[2], $link );
					continue;
				}

				$out[] = self::inline( $trim, $link );
			}
			if ( $list ) {
				$out[] = self::list_html( $list );
			}
			if ( ! $out ) {
				continue;
			}

			// Consecutive lines share a paragraph; lists stand on their own.
			$run = array();
			foreach ( $out as $piece ) {
				if ( 0 === strpos( $piece, '<ul' ) ) {
					if ( $run ) {
						$html .= '<p style="' . $p . '">' . implode( '<br>', $run ) . '</p>';
						$run   = array();
					}
					$html .= $piece;
					continue;
				}
				$run[] = $piece;
			}
			if ( $run ) {
				$html .= '<p style="' . $p . '">' . implode( '<br>', $run ) . '</p>';
			}
		}

		return $html;
	}

	/**
	 * Escape a text fragment and make its URLs and e-mail addresses clickable.
	 *
	 * @param string $text Text.
	 * @param string $link Link style.
	 */
	private static function inline( string $text, string $link ): string {
		$html = make_clickable( esc_html( $text ) );
		return str_replace( '<a href=', '<a style="' . esc_attr( $link ) . '" href=', $html );
	}

	/**
	 * Build a list.
	 *
	 * @param string[] $items Item HTML.
	 */
	private static function list_html( array $items ): string {
		return '<ul style="margin:0 0 16px;padding-left:20px;"><li style="margin:0 0 4px;">' . implode( '</li><li style="margin:0 0 4px;">', $items ) . '</li></ul>';
	}

	/**
	 * Readable button label for a URL.
	 *
	 * @param string $url URL.
	 */
	private static function short_url( string $url ): string {
		$parts = wp_parse_url( $url );
		$label = ( $parts['host'] ?? '' ) . ( $parts['path'] ?? '' );
		$label = rtrim( $label, '/' );
		return strlen( $label ) > 48 ? substr( $label, 0, 45 ) . '…' : $label;
	}
}
