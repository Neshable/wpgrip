<?php

namespace App\Services;

use SapientPro\ImageComparator\ImageComparator;

use Filament\Notifications\Notification;

class ImageComparision {


    /**
     * The comparator object
     * https://github.com/sapientpro/image-comparator
     *
     * @var ImageComparator
     */
    public $imageComparator;


    /**
     * The
     *
     * @param string $ip
     * @param integer $port
     */
    public function __construct()
    {
        $this->imageComparator = new ImageComparator();
    }

    public function compare_images( string $image1 = '', string $image2 = '')
    {
            try 
            {
                $result =  $this->imageComparator->compare($image1, $image2);
                return $result;
            }
            catch (Exception $e)
            {
                // What to do here?
                return false;
            }
        return false;
    }

    /**
     * Get the hash of the image so we can store in the database.
     *
     * @param string $image
     * @return void
     */
    public function hash_image( string $image = '' )
    {
        if ( $image && '' != $image )
        {
            try 
            {
                $hash = $this->imageComparator->hashImage( $image );
                if ( is_array($hash) )
                {
                    return serialize($hash);
                }
                //return $hash;
            }
            catch (Exception $e)
            {
                // What to do here?
                return false;
            }
        }
        return false;          
    }

    /**
     * Check if the result is for similar images
     *
     * @param float $result
     * @return boolean
     */
    public static function is_result_similar_images( float $result )
    {
        if ( $result )
        {
            if ( $result > 92 )
            {
                return true;
            } 
            else 
            {
                return false;
            }
        }
        return false;
    }
    
}