<?php

namespace RR\libs;

use GdImage;

/* ***********************************************
*  nome   : ImageThumb class
*  autor  : Calvin
*  data   : May/2005
* ***********************************************
*/

class ImageThumb
{
	public $thumb_width;
	public $thumb_height;
	public $img_quality;
	public $img_opacity;
	public $use_grayscale;
	public $resize_mode;

	public function __construct()
	{
		$this->thumb_width = 60;		// default width image to thumbnail
		$this->thumb_height = 60;		// default height image to thumbnail

		$this->img_quality = 75;		// quality percente of generated image (works only to JPEG outputs)
		$this->img_opacity = 50; 		// opacity percente when merging 2 images
		$this->use_grayscale = false; // grayscale the thumbnail
		$this->resize_mode = 2;
		/** $resize_mode:*/
	}
	/**
	 * Initialising class ImageThumb
	 * @param string	$format 	File format to generate
	 * @param int	$mode 		Wich type of process to use to resize image
	 * @desc Initialising class ImageThumb
	 */
	public function ImageThumb($format = 'JPG', $mode = 1)
	{
		$this->format = strtolower($format);
		$this->resize_mode = $mode;
	}

	public function set_grayscale()
	{
		$this->use_grayscale = true;
	}

	/**
	 * Create thumbnail file
	 * @param string $source     Full path to source file
	 * @param string $dest		Full path to save thumbnail as file
	 * @desc Create a thumbnail image from $source, saving it to $dest
	 */
	public function thumbnail($source, $dest)
	{
		$im = $this->_img_create_from($source);
		$source_w = ImageSX($im);
		$source_h = ImageSY($im);


		switch ($this->resize_mode) {
				// discart aspect ratio
			case 0:
				$ni = $this->_img_create($this->thumb_width, $this->thumb_height);
				$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $source_w, $source_h);
				break;
				// aspect ratio
			case 1:
				if ($source_w > $source_h) {
					$thumb_width = $this->thumb_width;
					$thumb_height = ($source_h * $this->thumb_width) / $source_w;
				} else {
					$thumb_height = $this->thumb_height;
					$thumb_width = ($source_w * $this->thumb_width) / $source_h;
				}
				$ni = $this->_img_create($thumb_width, $thumb_height);
				$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $source_w, $source_h);
				break;
				// Smart Cut = Crop
			case 2:
			default:
				$wm = $source_w / $this->thumb_width;
				$hm = $source_h / $this->thumb_height;

				$h_height = $this->thumb_height / 2;
				$w_height = $this->thumb_width / 2;

				$ni = $this->_img_create($this->thumb_width, $this->thumb_height);

				// landscape
				if ($source_w > $source_h) {
					$adjusted_width = $source_w / $hm;
					$half_width = $adjusted_width / 2;
					$int_width = $half_width - $w_height;
					$this->_img_copy_resize($ni, $im, -$int_width, 0, 0, 0, $adjusted_width, $this->thumb_height, $source_w, $source_h);
				}
				// portrade
				elseif (($source_w < $source_h) || ($source_w == $source_h)) {
					$adjusted_height = $source_h / $wm;
					$half_height = $adjusted_height / 2;
					$int_height = $half_height - $h_height;
					$this->_img_copy_resize($ni, $im, 0, -$int_height, 0, 0, $this->thumb_width, $adjusted_height, $source_w, $source_h);
				} else {
					$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $this->thumb_width, $this->thumb_heigth, $source_w, $source_h);
				}
		}
		imagedestroy($im);
		if ($this->use_grayscale) {
			$ni = $this->_img_grayscale($ni);
		}
		$this->output($ni, $dest);
	}
	/**
	 * Add watermark to image
	 * @param string $image			Full path to source file
	 * @param string $watermark		Full path to watermark image
	 * @param string $new_file		Full path to where you want save the resulted image,	
	 *									if blank, the original image will be replaced!
	 * @param int $watermark_pos		Position where $insertfile will be inserted in $source
	 *									0 = middle
	 *									1 = top left
	 *									2 = top right
	 *									3 = bottom right
	 *									4 = bottom left
	 *									5 = top middle
	 *									6 = middle right
	 *									7 = bottom middle
	 *									8 = middle left
	 * @desc Add a watermark image to some other image, selecting where place it.
	 */
	public function watermark($image, $watermark, $new_file = '', $watermark_pos = 0)
	{
		if ($new_file == '') {
			$new_file = $image;
		}

		$im_image = $this->_img_create_from($image);
		$image_width = imageSX($im_image);
		$image_height = imageSY($im_image);

		$im_watermark = $this->_img_create_from($watermark);
		$water_width = imageSX($im_watermark);
		$water_height = imageSY($im_watermark);

		switch ($watermark_pos) {
			case 0: //middle
				$pos_x = ($image_width / 2) - ($water_width / 2);
				$pos_y = ($image_height / 2) - ($water_height / 2);
				break;
			case 1:  //top left
				$pos_x = 0;
				$pos_y = 0;
				break;
			case 2: //top right
				$pos_x = $image_width - $water_width;
				$pos_y = 0;
				break;
			case 3: //bottom right
				$pos_x = $image_width - $water_width;
				$pos_y = $image_height - $water_height;
				break;
			case 4: //bottom left
				$pos_x = 0;
				$pos_y = $image_height - $water_height;
				break;
			case 5: //top middle
				$pos_x = (($image_width - $water_width) / 2);
				$pos_y = 0;
				break;
			case 6: //middle right
				$pos_x = $image_width - $water_width;
				$pos_y = ($image_height / 2) - ($water_height / 2);
				break;
			case 7: //bottom middle
				$pos_x = (($image_width - $water_width) / 2);
				$pos_y = $image_height - $water_height;
				break;
			case 8: //middle left
				$pos_x = 0;
				$pos_y = ($image_height / 2) - ($water_height / 2);
				break;
		}
		if (function_exists('imagecopymerge')) {
			imagecopymerge($im_image, $im_watermark, $pos_x, $pos_y, 0, 0, $water_width, $water_height, $this->img_opacity);
		} else {
			imagecopy($im_image, $im_watermark, $pos_x, $pos_y, 0, 0, $water_width, $water_height);
		}
		$this->output($im_image, $new_file);
	}

	/**
	 * Convert color image to grayscale
	 * @param string $source     Full path to source file
	 * @param string $dest		Full path to save thumbnail as file
	 * @desc Convert an color image to grayscale
	 */
	public function grayscale($source, $dest)
	{
		$im = $this->_img_create_from($source);
		$im = $this->_img_grayscale($im);
		$this->output($im, $dest);
	}

	/**
	 * Output image
	 * @param mixed $im			Image identifier
	 * @param string $filename	Full path to save thumbnail as file,
	 *								leaving blank script output the image to webbrowser
	 * @desc Output the image to a file or to browser
	 */
	public function output($im, $filename = '')
	{
		if ($filename == '') {
			switch ($this->format) {
				case 'gif':
					header("Content-type: image/gif");
					imageGif($im);
					break;
				case 'png':
					header("Content-type: image/png");
					imagePng($im);
				case 'bmp':
					header("Content-type: image/vnd.wap.wbmp");
					imageWbmp($im);
					break;
				case 'jpg':
				case 'jpeg':
				default:
					header("Content-type: image/jpeg");
					imageJpeg($im, '', $this->img_quality);
			}
			@imagedestroy($im);
			exit;
		} else {
			switch ($this->format) {
				case 'png':
					imagePng($im, $filename);
					break;
				case 'gif':
					// only GD < ver. 1.6
					imageGif($im, $filename);
					break;
				case 'bmp':
					// only GD ver 1.8 or greater
					imageWbmp($im, $filename);
					break;
				case 'jpeg':
				case 'jpg':
				default:
					imageJpeg($im, $filename, $this->img_quality);
			}
			return true;
		}
	}


	// priv functions
	public function _img_grayscale($im)
	{
		$x = imagesx($im);
		$y = imagesy($im);
		for ($i = 0; $i < $y; $i++) {
			for ($j = 0; $j < $x; $j++) {
				$pos = imagecolorat($im, $j, $i);
				$f = imagecolorsforindex($im, $pos);
				$gst = $f['red'] * 0.15 + $f['green'] * 0.5 + $f['blue'] * 0.35;
				$col = imagecolorresolve($im, $gst, $gst, $gst);
				imagesetpixel($im, $j, $i, $col);
			}
		}
		return $im;
	}

	public function _img_copy_resize(&$dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH)
	{
		if (function_exists('imagecopyresampled')) {
			if (!@ImageCopyResampled($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH)) {
				if (imagecopyresized($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH)) {
					return true;
				}
			}
			return true;
		} else {
			if (imagecopyresized($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH)) {
				return true;
			}
		}
		return false;
	}

	public function _img_create($x_size, $y_size)
	{
		if (function_exists('imageCreateTrueColor')) {
			if ($im = imagecreatetruecolor($x_size, $y_size)) {
				return $im;
			}
		} else {
			if ($im = imageCreate($x_size, $y_size)) {
				return $im;
			}
		}
		// $this->debug();
	}

	public function _img_create_from($file)
	{
		$ext = $this->_get_ext($file);

		switch ($ext) {
			case 'jpeg':
			case 'jpg':
				$im = imageCreateFromJpeg($file);
				break;
			case 'png':
				$im = imageCreateFromPng($file);
				break;
			case 'gif':
				$im = imageCreateFromGif($file);
				break;
		}
		return $im;
	}

	public function _get_ext($file)
	{
		$ext_arr = explode(".", strtolower($file));
		$n = count($ext_arr) - 1;
		return $ext_arr[$n];
	}
}
