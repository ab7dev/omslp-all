<?php

/**
 * Video integration related functions
 */


/* Vimeography
 * https://docs.vimeography.com/article/38-how-do-i-display-my-private-hidden-restricted-videos
 * Display Videos with a Domain Restriction
 */

function adjust_vimeography_privacy_filter() {
  return 'none';
}

add_filter('vimeography.request.privacy.filter', 'adjust_vimeography_privacy_filter');