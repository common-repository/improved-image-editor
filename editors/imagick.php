<?php

class Improved_Image_Editor_Imagick extends WP_Image_Editor_Imagick {

	/**
	 * Resize multiple images from a single source.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param array $sizes {
	 *     An array of image size arrays. Default sizes are 'small', 'medium', 'large'.
	 *
	 *     Either a height or width must be provided.
	 *     If one of the two is set to null, the resize will
	 *     maintain aspect ratio according to the provided dimension.
	 *
	 *     @type array $size {
	 *         @type int  ['width']  Optional. Image width.
	 *         @type int  ['height'] Optional. Image height.
	 *         @type bool $crop   Optional. Whether to crop the image. Default false.
	 *     }
	 * }
	 * @return array An array of resized images' metadata by size.
	 */
	public function multi_resize( $sizes ) {
		$metadata = array();
		$orig_size = $this->size;
		$orig_image = $this->image->getImage();
		$orig_quality = $this->get_quality();

		foreach ( $sizes as $size => $size_data ) {
			if ( ! $this->image ) {
				$this->image = $orig_image->getImage();
				$this->set_quality( $orig_quality );
			}

			if ( ! isset( $size_data['width'] ) && ! isset( $size_data['height'] ) ) {
				continue;
			}

			if ( ! isset( $size_data['width'] ) ) {
				$size_data['width'] = null;
			}
			if ( ! isset( $size_data['height'] ) ) {
				$size_data['height'] = null;
			}

			if ( ! isset( $size_data['crop'] ) ) {
				$size_data['crop'] = false;
			}

			$size_data = Improved_Image_Editor::_editor_update_size_data( $size_data, $this, $size );

			$resize_result = $this->resize( $size_data['width'], $size_data['height'], $size_data['crop'] );

			if( ! is_wp_error( $resize_result ) ) {
				Improved_Image_Editor::_editor_update_image( $this, $size );

				$resized = $this->_save( $this->image );

				$this->image->clear();
				$this->image->destroy();
				$this->image = null;

				if ( ! is_wp_error( $resized ) && $resized ) {
					unset( $resized['path'] );
					$metadata[$size] = $resized;
				}
			}

			$this->size = $orig_size;
		}

		$this->image = $orig_image;

		return $metadata;
	}


	public function filter_grayscale() {
		$this->image->setImageColorSpace( Imagick::COLORSPACE_GRAY );
	}

	public function filter_sepia() {
		$this->image->sepiaToneImage( 80 );
	}

	public function filter_contrast() {
		$this->image->contrastImage( 1 );
	}

	public function filter_edge() {
		$this->image->edgeImage( 0 );
	}

	public function filter_emboss() {
		$this->image->embossImage( 0, 1 );
	}

	public function filter_gaussian_blur() {
		$this->image->gaussianBlurImage( 2, 3 );
	}

	public function filter_selective_blur() {
		$this->image->blurImage( 5, 3 );
	}

	public function filter_negative() {
		$this->image->negateImage( false );
	}

}