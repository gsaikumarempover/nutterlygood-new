<?php
/**
 * Central media paths, WebP helpers, and responsive image output.
 */

if ( ! function_exists( 'nuttergood_farmley_media_folders' ) ) {
	/**
	 * @return array<string, string> Folder key => uploads-relative directory.
	 */
	function nuttergood_farmley_media_folders() {
		return array(
			'products'   => 'ng-media/products',
			'categories' => 'ng-media/categories',
			'banners'    => 'ng-media/banners',
			'heroes'     => 'ng-media/heroes',
			'logos'      => 'ng-media/logos',
			'blog'       => 'ng-media/blog',
			'about'      => 'ng-media/about',
			'slider'     => 'ng-media/slider',
			'shop'       => 'ng-media/shop',
			'misc'       => 'ng-media/misc',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_media_legacy_map' ) ) {
	/**
	 * Old 2026/06 paths → ng-media folder key (for backward-compatible URLs).
	 *
	 * @return array<string, string>
	 */
	function nuttergood_farmley_media_legacy_map() {
		return array(
			'2026/06/ai-products'      => 'products',
			'2026/06/hd-products'      => 'products',
			'2026/06/ng-branded-packets' => 'products',
			'2026/06/categories'       => 'categories',
			'2026/06/category-thumbs'  => 'categories',
			'2026/06/ai-categories'    => 'categories',
			'2026/06/banners'          => 'banners',
			'2026/06/about'            => 'about',
			'2026/06/blog'             => 'blog',
			'2026/06/slider'           => 'slider',
			'2026/06/slider/hd-heroes/source' => 'slider',
			'2026/06/shop-assets'      => 'shop',
		);
	}
}

if ( ! function_exists( 'nuttergood_farmley_media_rel' ) ) {
	/**
	 * Build uploads-relative path for a categorized asset.
	 *
	 * @param string $folder Folder key from nuttergood_farmley_media_folders().
	 * @param string $file   Filename.
	 */
	function nuttergood_farmley_media_rel( $folder, $file ) {
		$folders = nuttergood_farmley_media_folders();
		$dir     = isset( $folders[ $folder ] ) ? $folders[ $folder ] : 'ng-media/misc';
		return trailingslashit( $dir ) . ltrim( $file, '/' );
	}
}

if ( ! function_exists( 'nuttergood_farmley_media_url' ) ) {
	/**
	 * Public URL for a categorized media file.
	 *
	 * @param string $folder Folder key.
	 * @param string $file   Filename.
	 */
	function nuttergood_farmley_media_url( $folder, $file ) {
		return content_url( 'uploads/' . nuttergood_farmley_media_rel( $folder, $file ) );
	}
}

if ( ! function_exists( 'nuttergood_farmley_uploads_url' ) ) {
	/**
	 * Resolve legacy or ng-media uploads path to a URL (file must exist).
	 *
	 * @param string $relative Path under uploads/ (legacy or ng-media).
	 */
	function nuttergood_farmley_uploads_url( $relative ) {
		$relative = ltrim( str_replace( '\\', '/', $relative ), '/' );
		$abs      = WP_CONTENT_DIR . '/uploads/' . $relative;

		if ( file_exists( $abs ) ) {
			return content_url( 'uploads/' . $relative );
		}

		foreach ( nuttergood_farmley_media_legacy_map() as $legacy_prefix => $folder_key ) {
			if ( 0 !== strpos( $relative, $legacy_prefix ) ) {
				continue;
			}
			$filename = substr( $relative, strlen( $legacy_prefix ) + 1 );
			$filename = str_replace( 'hd-heroes/source/', '', $filename );
			$new_rel  = nuttergood_farmley_media_rel( $folder_key, $filename );
			$new_abs  = WP_CONTENT_DIR . '/uploads/' . $new_rel;
			if ( file_exists( $new_abs ) ) {
				return content_url( 'uploads/' . $new_rel );
			}

			if ( str_ends_with( strtolower( $filename ), '.png' ) ) {
				$webp_rel = nuttergood_farmley_media_rel( $folder_key, substr( $filename, 0, -4 ) . '.webp' );
				$webp_abs = WP_CONTENT_DIR . '/uploads/' . $webp_rel;
				if ( file_exists( $webp_abs ) ) {
					return content_url( 'uploads/' . $webp_rel );
				}
			}
		}

		return content_url( 'uploads/' . $relative );
	}
}

if ( ! function_exists( 'nuttergood_farmley_resolve_legacy_media_url' ) ) {
	/**
	 * Map old 2026/06 upload URLs to ng-media (and PNG → WebP when needed).
	 *
	 * @param string $url Absolute or root-relative uploads URL.
	 */
	function nuttergood_farmley_resolve_legacy_media_url( $url ) {
		if ( ! is_string( $url ) || '' === $url ) {
			return $url;
		}

		$uploads = wp_get_upload_dir();
		$base    = $uploads['baseurl'];
		if ( false === strpos( $url, $base ) && false === strpos( $url, '/wp-content/uploads/' ) ) {
			return $url;
		}

		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( ! is_string( $path ) || false === strpos( $path, '/wp-content/uploads/' ) ) {
			return $url;
		}

		$relative = ltrim( substr( $path, strpos( $path, '/wp-content/uploads/' ) + strlen( '/wp-content/uploads/' ) ), '/' );
		$resolved = nuttergood_farmley_uploads_url( $relative );

		if ( $resolved && $resolved !== content_url( 'uploads/' . $relative ) ) {
			return $resolved;
		}

		return $url;
	}
}

if ( ! function_exists( 'nuttergood_farmley_upload_path_from_url' ) ) {
	/**
	 * @param string $url Attachment URL.
	 */
	function nuttergood_farmley_upload_path_from_url( $url ) {
		if ( ! is_string( $url ) || '' === $url ) {
			return '';
		}

		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( ! is_string( $path ) || false === strpos( $path, '/wp-content/uploads/' ) ) {
			return '';
		}

		$uploads  = wp_get_upload_dir();
		$relative = ltrim( substr( $path, strpos( $path, '/wp-content/uploads/' ) + strlen( '/wp-content/uploads/' ) ), '/' );

		return wp_normalize_path( trailingslashit( $uploads['basedir'] ) . $relative );
	}
}

if ( ! function_exists( 'nuttergood_farmley_upload_url_exists' ) ) {
	/**
	 * @param string $url Attachment URL.
	 */
	function nuttergood_farmley_upload_url_exists( $url ) {
		$path = nuttergood_farmley_upload_path_from_url( $url );

		return '' !== $path && file_exists( $path );
	}
}

if ( ! function_exists( 'nuttergood_farmley_get_existing_attachment_url' ) ) {
	/**
	 * Resolve a working URL when WordPress metadata points at a deleted intermediate size.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size          Requested size slug.
	 */
	function nuttergood_farmley_get_existing_attachment_url( $attachment_id, $size = 'full' ) {
		$attachment_id = (int) $attachment_id;
		if ( $attachment_id <= 0 ) {
			return '';
		}

		$master_path = get_attached_file( $attachment_id );
		if ( $master_path && file_exists( $master_path ) ) {
			$uploads = wp_get_upload_dir();
			$rel     = ltrim( str_replace( wp_normalize_path( $uploads['basedir'] ), '', wp_normalize_path( $master_path ) ), '/\\' );

			return nuttergood_farmley_resolve_legacy_media_url( content_url( 'uploads/' . str_replace( '\\', '/', $rel ) ) );
		}

		if ( ! $master_path ) {
			return '';
		}

		$dir      = dirname( $master_path );
		$filename = pathinfo( $master_path, PATHINFO_FILENAME );
		$targets  = array();

		if ( is_string( $size ) && '' !== $size && 'full' !== $size ) {
			$dims = function_exists( 'wp_get_registered_image_subsizes' ) ? wp_get_registered_image_subsizes() : array();
			if ( isset( $dims[ $size ]['width'] ) ) {
				$target_w = (int) $dims[ $size ]['width'];
				foreach ( array( 400, 640, 960, 1280 ) as $w ) {
					if ( $w >= ( $target_w * 0.75 ) ) {
						$targets[] = $dir . '/' . $filename . '-' . $w . 'w.webp';
					}
				}
			}
		}

		$targets[] = $dir . '/' . $filename . '.webp';

		foreach ( $targets as $candidate ) {
			if ( ! file_exists( $candidate ) ) {
				continue;
			}

			$uploads = wp_get_upload_dir();
			$rel     = ltrim( str_replace( wp_normalize_path( $uploads['basedir'] ), '', wp_normalize_path( $candidate ) ), '/\\' );

			return content_url( 'uploads/' . str_replace( '\\', '/', $rel ) );
		}

		return '';
	}
}

if ( ! function_exists( 'nuttergood_farmley_filter_attachment_url' ) ) {
	function nuttergood_farmley_filter_attachment_url( $url, $attachment_id = 0 ) {
		$url = nuttergood_farmley_resolve_legacy_media_url( $url );

		if ( $attachment_id && ! nuttergood_farmley_upload_url_exists( $url ) ) {
			$fallback = nuttergood_farmley_get_existing_attachment_url( $attachment_id, 'full' );
			if ( $fallback ) {
				return $fallback;
			}
		}

		return $url;
	}
	add_filter( 'wp_get_attachment_url', 'nuttergood_farmley_filter_attachment_url', 15, 2 );
}

if ( ! function_exists( 'nuttergood_farmley_filter_attachment_image_src' ) ) {
	function nuttergood_farmley_filter_attachment_image_src( $image, $attachment_id = 0, $size = '' ) {
		if ( ! is_array( $image ) || empty( $image[0] ) ) {
			return $image;
		}

		$image[0] = nuttergood_farmley_resolve_legacy_media_url( $image[0] );

		if ( $attachment_id && ! nuttergood_farmley_upload_url_exists( $image[0] ) ) {
			$fallback = nuttergood_farmley_get_existing_attachment_url( $attachment_id, $size );
			if ( $fallback ) {
				$image[0] = $fallback;

				$meta = wp_get_attachment_metadata( $attachment_id );
				if ( is_array( $meta ) && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
					$image[1] = (int) $meta['width'];
					$image[2] = (int) $meta['height'];
				}
			}
		}

		return $image;
	}
	add_filter( 'wp_get_attachment_image_src', 'nuttergood_farmley_filter_attachment_image_src', 12, 3 );
}

if ( ! function_exists( 'nuttergood_farmley_filter_attachment_image_url' ) ) {
	function nuttergood_farmley_filter_attachment_image_url( $url, $attachment_id, $size ) {
		$url = nuttergood_farmley_resolve_legacy_media_url( $url );

		if ( $attachment_id && ! nuttergood_farmley_upload_url_exists( $url ) ) {
			$fallback = nuttergood_farmley_get_existing_attachment_url( $attachment_id, $size );
			if ( $fallback ) {
				return $fallback;
			}
		}

		return $url;
	}
	add_filter( 'wp_get_attachment_image_url', 'nuttergood_farmley_filter_attachment_image_url', 12, 3 );
}

if ( ! function_exists( 'nuttergood_farmley_media_webp_path' ) ) {
	/**
	 * @param string $abs_path Absolute path to source image.
	 * @param int    $width    Target width (0 = full).
	 */
	function nuttergood_farmley_media_webp_path( $abs_path, $width = 0 ) {
		if ( ! file_exists( $abs_path ) ) {
			return '';
		}

		$dir  = dirname( $abs_path );
		$base = pathinfo( $abs_path, PATHINFO_FILENAME );
		$suffix = $width > 0 ? '-' . (int) $width . 'w' : '';
		$webp = $dir . '/' . $base . $suffix . '.webp';

		return $webp;
	}
}

if ( ! function_exists( 'nuttergood_farmley_media_generate_webp' ) ) {
	/**
	 * Generate WebP (and optional resized WebP) via GD or ImageMagick CLI.
	 *
	 * @param string $abs_path Source image path.
	 * @param int    $width    Max width (0 = same dimensions).
	 * @param int    $quality  WebP quality 1–100.
	 */
	function nuttergood_farmley_media_generate_webp( $abs_path, $width = 0, $quality = 82 ) {
		if ( ! file_exists( $abs_path ) ) {
			return '';
		}

		$out = nuttergood_farmley_media_webp_path( $abs_path, $width );
		if ( file_exists( $out ) && filemtime( $out ) >= filemtime( $abs_path ) ) {
			return $out;
		}

		$quality = max( 60, min( 92, (int) $quality ) );

		if ( extension_loaded( 'imagick' ) ) {
			try {
				$img = new Imagick( $abs_path );
				if ( $width > 0 ) {
					$img->resizeImage( $width, 0, Imagick::FILTER_LANCZOS, 1, true );
				}
				$img->setImageFormat( 'webp' );
				$img->setImageCompressionQuality( $quality );
				$img->writeImage( $out );
				$img->destroy();
				return $out;
			} catch ( Exception $e ) {
				// Fall through to GD / CLI.
			}
		}

		if ( extension_loaded( 'gd' ) ) {
			$info = @getimagesize( $abs_path );
			if ( $info ) {
				$src = null;
				switch ( $info[2] ) {
					case IMAGETYPE_JPEG:
						$src = @imagecreatefromjpeg( $abs_path );
						break;
					case IMAGETYPE_PNG:
						$src = @imagecreatefrompng( $abs_path );
						break;
					case IMAGETYPE_WEBP:
						return $abs_path;
				}
				if ( $src ) {
					// GD cannot write palette PNGs directly to WebP — promote to truecolor first.
					if ( function_exists( 'imagepalettetotruecolor' ) ) {
						@imagepalettetotruecolor( $src );
					}
					imagealphablending( $src, true );
					imagesavealpha( $src, true );

					$w = imagesx( $src );
					$h = imagesy( $src );
					if ( $width > 0 && $w > $width ) {
						$nh  = (int) round( $h * ( $width / $w ) );
						$dst = imagecreatetruecolor( $width, $nh );
						imagealphablending( $dst, false );
						imagesavealpha( $dst, true );
						imagecopyresampled( $dst, $src, 0, 0, 0, 0, $width, $nh, $w, $h );
						imagedestroy( $src );
						$src = $dst;
					}
					if ( function_exists( 'imagewebp' ) && @imagewebp( $src, $out, $quality ) ) {
						imagedestroy( $src );
						return file_exists( $out ) ? $out : '';
					}
					imagedestroy( $src );
				}
			}
		}

		$magick = '';
		foreach ( array( 'magick', 'convert' ) as $bin ) {
			$found = trim( (string) shell_exec( 'where ' . $bin . ' 2>nul' ) );
			if ( $found ) {
				$magick = strtok( $found, "\n" );
				break;
			}
		}

		if ( $magick ) {
			$resize = $width > 0 ? '-resize ' . (int) $width . 'x' : '';
			$cmd    = escapeshellarg( $magick ) . ' ' . escapeshellarg( $abs_path ) . ' ' . $resize . ' -quality ' . $quality . ' ' . escapeshellarg( $out ) . ' 2>nul';
			@exec( $cmd );
			if ( file_exists( $out ) ) {
				return $out;
			}
		}

		return '';
	}
}

if ( ! function_exists( 'nuttergood_farmley_responsive_image' ) ) {
	/**
	 * Output a picture element with WebP + fallback srcset.
	 *
	 * @param string $folder   Media folder key.
	 * @param string $file     Filename.
	 * @param array  $args     width, height, sizes, class, alt, loading, widths[].
	 */
	function nuttergood_farmley_responsive_image( $folder, $file, $args = array() ) {
		$defaults = array(
			'width'   => 0,
			'height'  => 0,
			'sizes'   => '100vw',
			'class'   => '',
			'alt'     => '',
			'loading' => 'lazy',
			'widths'  => array( 400, 640, 960, 1280 ),
		);
		$args     = wp_parse_args( $args, $defaults );

		$rel = nuttergood_farmley_media_rel( $folder, $file );
		$abs = WP_CONTENT_DIR . '/uploads/' . $rel;

		if ( ! file_exists( $abs ) ) {
			$url = nuttergood_farmley_uploads_url( '2026/06/' . $folder . '/' . $file );
			echo '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $args['alt'] ) . '" loading="' . esc_attr( $args['loading'] ) . '" />';
			return;
		}

		$fallback_url = content_url( 'uploads/' . $rel );
		$webp_srcset  = array();
		$img_srcset   = array();

		foreach ( (array) $args['widths'] as $w ) {
			$w = (int) $w;
			if ( $w < 1 ) {
				continue;
			}
			$webp = nuttergood_farmley_media_generate_webp( $abs, $w );
			if ( $webp ) {
				$webp_srcset[] = content_url( 'uploads/' . str_replace( WP_CONTENT_DIR . '/uploads/', '', $webp ) ) . ' ' . $w . 'w';
			}
		}

		$full_webp = nuttergood_farmley_media_generate_webp( $abs, 0 );
		$attr_w    = $args['width'] > 0 ? ' width="' . (int) $args['width'] . '"' : '';
		$attr_h    = $args['height'] > 0 ? ' height="' . (int) $args['height'] . '"' : '';
		$class     = $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : '';

		echo '<picture>';
		if ( $full_webp || $webp_srcset ) {
			$webp_url = $full_webp
				? content_url( 'uploads/' . str_replace( WP_CONTENT_DIR . '/uploads/', '', $full_webp ) )
				: strtok( implode( ', ', $webp_srcset ), ' ' );
			echo '<source type="image/webp" srcset="' . esc_attr( $webp_srcset ? implode( ', ', $webp_srcset ) : $webp_url ) . '" sizes="' . esc_attr( $args['sizes'] ) . '" />';
		}
		printf(
			'<img src="%1$s" alt="%2$s" loading="%3$s"%4$s%5$s%6$s />',
			esc_url( $fallback_url ),
			esc_attr( $args['alt'] ),
			esc_attr( $args['loading'] ),
			$class,
			$attr_w,
			$attr_h
		);
		echo '</picture>';
	}
}

if ( ! function_exists( 'nuttergood_farmley_media_upload_dir' ) ) {
	function nuttergood_farmley_media_upload_dir( $dirs ) {
		if ( is_admin() && isset( $_POST['ng_media_folder'] ) ) {
			$folder = sanitize_key( wp_unslash( $_POST['ng_media_folder'] ) );
			$map    = nuttergood_farmley_media_folders();
			if ( isset( $map[ $folder ] ) ) {
				$dirs['subdir'] = '/' . $map[ $folder ];
				$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
				$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
			}
		}
		return $dirs;
	}
	add_filter( 'upload_dir', 'nuttergood_farmley_media_upload_dir' );
}

if ( ! function_exists( 'nuttergood_farmley_attachment_webp_src' ) ) {
	function nuttergood_farmley_attachment_webp_src( $image, $attachment_id, $size ) {
		if ( ! $image || empty( $image[0] ) ) {
			return $image;
		}

		$path = get_attached_file( $attachment_id );
		if ( ! $path || ! file_exists( $path ) ) {
			return $image;
		}

		// Use pre-generated WebP only — never convert during page render (too slow).
		$webp = nuttergood_farmley_media_webp_path( $path, 0 );
		if ( $webp && file_exists( $webp ) ) {
			$image['ng_webp'] = content_url( 'uploads/' . str_replace( WP_CONTENT_DIR . '/uploads/', '', $webp ) );
		}

		return $image;
	}
	add_filter( 'wp_get_attachment_image_src', 'nuttergood_farmley_attachment_webp_src', 20, 3 );
}

if ( ! function_exists( 'nuttergood_farmley_picture_attachment_image' ) ) {
	function nuttergood_farmley_picture_attachment_image( $html, $attachment_id, $size, $icon, $attr ) {
		$src = wp_get_attachment_image_src( $attachment_id, $size );
		if ( empty( $src['ng_webp'] ) ) {
			return $html;
		}

		$webp = esc_url( $src['ng_webp'] );
		return '<picture><source type="image/webp" srcset="' . $webp . '" />' . $html . '</picture>';
	}
	add_filter( 'wp_get_attachment_image', 'nuttergood_farmley_picture_attachment_image', 20, 5 );
}