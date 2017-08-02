<?php


function wppp_view_all_print_image_title ( $meta, $all_sizes ) {
    ?>
    <tr><td colspan="<?php echo count( $all_sizes ) + 2; ?>">
            <hr/>
            &rarr;
            <span>
						<?php
                        echo print_r($meta['file'], true);
                        ?>
						</span>
        </td></tr>
    <?php

}

function wppp_view_all_print_is_compressed( $meta ) {

    ?>
    <tr>
    <td>
        <?php
        if ( isset( $meta['pixpie_compressed'] ) && $meta['pixpie_compressed'] ) {
            ?>
            +
            <?php
        } else {
            ?>
            -
            <?php
        }
        ?>
    </td>
    <?php

}


function wppp_view_all_print_original_image ($image_id, $dimensions, $img_in_size, $original_file_path ) {

    ?>
    <td width="100px" style="min-width: 100px; max-width: 100px;">
        <?php
        if ( isset( $img_in_size ) && wp_attachment_is_image( $image_id ) ) {
            ?>
            <a href="<?php echo( $img_in_size ); ?>" target="_blank">
                <img src="<?php echo( $img_in_size ); ?>" style="width: 100%; height: auto;" />
            </a>
            <?php
            $file_size = filesize( $original_file_path );
            $file_size = wppp_get_display_file_size( $file_size );
            ?>
            <div style="font-size: 10px"><?php echo( $dimensions ); ?> - <?php echo( $file_size ); ?></div>
            <?php
        }
        ?>
    </td>
    <?php

}


function wppp_view_all_print_orig_uncomp_img ($meta, $image_id, $original_file_name, $original_file_extension, $upload_dir) {

    // full uncomp
    $uncompressed_filename = '';
    if (
        isset( $meta ) &&
        isset( $meta['file'] ) &&
        isset( $meta['pixpie_compressed'] ) &&
        $meta['pixpie_compressed']
    ){
        $image = wp_get_attachment_image_src( $image_id, 'full', false );
        $dimensions = $image[1] . 'x' . $image[2];

        $uncompressed_filename =
            $upload_dir['url'] . '/' . $original_file_name . FILENAME_UNCOMP . '.' . $original_file_extension;
        $uncompressed_file_path =
            $upload_dir['path'] . '/' .
            $original_file_name . FILENAME_UNCOMP . '.' . $original_file_extension;

        ?>
        <td width="100px" style="min-width: 100px; max-width: 100px;">
        <?php
        if ( file_exists( $uncompressed_file_path ) ) {
            ?>
            <a href="<?php echo( $uncompressed_filename ); ?>" target="_blank">
                <img src="<?php echo( $uncompressed_filename ); ?>" style="width: 100%; height: auto;" />
            </a>
            <?php
            $file_size = filesize( $uncompressed_file_path );
            $file_size = wppp_get_formatted_file_size( $file_size );
            ?>
            <div style="font-size: 10px"><?php echo( $dimensions ); ?> - <?php echo( $file_size ); ?></div>
            </td>

            <?php
        } else {
            ?>
            &nbsp; <!-- file does not exist -->
            <?php
        }
        ?>
        <?php
    } else {
        ?><td>&nbsp; <!-- file is not compressed --></td><?php
    }
}


function wppp_view_all_print_image_size ( $image_id, $size_name, $meta, $upload_dir ) {
    $img_in_size = '';

    $image = wp_get_attachment_image_src( $image_id, $size_name, false );
    $dimensions = $image[1] . 'x' . $image[2];
    $img_in_size = wp_get_attachment_image_url( $image_id, $size_name, false );

    $meta_size = $meta['sizes'][ $size_name ];

    if ( isset( $meta_size ) && ( isset( $meta_size['file'] ) ) ) {

        $img_path_in_size = $upload_dir['path'] . '/' . $meta_size['file'];

        ?>
        <td width="100px" style="min-width: 100px; max-width: 100px;">
            <?php

            if (
                isset( $img_in_size ) &&
                wp_attachment_is_image( $image_id ) &&
                file_exists( $img_path_in_size )
            ) {

                // hide uncomp if not compressed
                if (
                    (
                        ! isset( $meta['pixpie_compressed'] ) ||
                        ( $meta['pixpie_compressed'] != true )
                    ) &&
                    (
                    wppp_ends_with( $size_name, SIZE_UNCOMP )
                    )
                ) {

                    ?>&nbsp; <!-- uncompressed image not shown if file is not compressed--><?php

                } else {

                    ?>

                    <a href="<?php echo( $img_in_size ); ?>" target="_blank">
                        <img src="<?php echo( $img_in_size ); ?>" style="width: 100%; height: auto;" />
                    </a>

                    <?php
                    $file_size = filesize( $img_path_in_size );
                    $file_size = wppp_get_formatted_file_size( $file_size );
                    $single_image_compressed =
                        $meta['sizes'][ $size_name ]['pixpie_compressed'];

                    if (
                        isset( $single_image_compressed ) &&
                        $single_image_compressed
                    ) {
                        $dimensions = '+ ' . $dimensions;
                    } else {
                        $dimensions = '- ' . $dimensions;
                    }

                    ?>

                    <div style="font-size: 10px">
                        <?php echo( $dimensions ); ?> - <?php echo( $file_size ); ?>
                    </div>

                    <?php

                }
            }

            ?>
        </td>
        <?php

    } else {

        ?><td>&nbsp; <!-- file not set --> </td><?php

    }

}