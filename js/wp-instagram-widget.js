/**
 * WP Instagram Widget JS
 * 
 * https://github.com/bitpiston/wp-instagram-widget-js
 * 
 * Copyright © 2020 BitPiston Studios
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of 
 * the GNU General Public License as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See 
 * the GNU General Public License for more details.
 */

(function($) {
    'use strict';

    $.wpInstagramWidget = function(options) {
        if (!options.username) {
            console.error('No Instagram username or tag.');
            return false;
        }

        var instagramUrl = 'https://www.instagram.com/',
            storageKey   = 'wpiwjs-';
        switch (options.username.substr(0, 1)) {
            case '#':
                var tag = options.username.replace('#', '');
                instagramUrl += 'explore/tags/' + tag + '/';
                storageKey   += 't-' + tag;
                break;

            default: 
                var username = options.username.replace('@', '');
                instagramUrl += username + '/';
                storageKey   += 'u-' + username;
                break;
        }

        let images = function() {
            var data = localStorage.getItem(storageKey),
                images = [];

            if (data) {
                data = JSON.parse(data);

                if (data.ttl < Date.now()) {
                    localStorage.removeItem(storageKey);
                } else {
                    images = data.images;
                }
            }

            return images;
        }();

        if (images.length > 0) {
            displayImages(images);
        } else {
            $.get(instagramUrl + '?__a=1', function(data) {
                data = data.graphql.user.edge_owner_to_timeline_media.edges || data.graphql.tag.edge_hashtag_to_media.edges;

                if (!data || !Array.isArray(data)) {
                    console.error('Instagram has returned invalid data.');
                    return false;
                }

                var images = [];

                if (options.images_only === true) {
                    data = data.filter(function(image) {
                        return image.is_video === false;
                    });
                }

                data = data.slice(0, options.limit);

                data.forEach(function(image) {
                    images.push({
                        'description': image.node.edge_media_to_caption.edges[0] ? image.node.edge_media_to_caption.edges[0].node.text : '',
                        'link': 'https://www.instagram.com/p/' + image.node.shortcode + '/',
                        'time': image.node.taken_at_timestamp,
                        'comments': image.node.edge_media_to_comment.count,
                        'likes': image.node.edge_liked_by.count,
                        'src': {
                            'thumbnail': image.node.thumbnail_resources[0].src.replace(/^https?:/i, ''),
                            'small': image.node.thumbnail_resources[2].src.replace(/^https?:/i, ''),
                            'large': image.node.thumbnail_resources[4].src.replace(/^https?:/i, ''),
                            'original': image.node.display_url.replace(/^https?:/i, '')
                        },
                        'type': image.node.is_video ? 'video' : 'image'
                    });
                });

                if (!images.length) {
                    console.error('Instagram did not return any images.');
                    return false;
                }

                localStorage.setItem(storageKey, JSON.stringify({
                    'images': images,
                    'ttl': Date.now() + Math.floor(options.cache * 1000)
                }));

                displayImages(images);

            }).fail(function(error) {
                console.error('Failed to retrieve data from Instagram. Response status code: ', error.status);
                return false;
            });
        }

        function escapeString(string) {
            let map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            };

            return string.replace(/[&<>"'`=\/]/g, function (character) {
                return map[character];
            });
        }

        function displayImages(images) {
            let rel = options.target === '_blank' ? 'noopener' : '';
            var $ul = $(options.container + ' ul');

            images.forEach(function(image) {
                var html = '';
                html += '<li class="' + options.classes.list.li + '">';
                html += '<a href="' + image.link + '" target="' + options.target + '" rel="' + rel + '"  class="' + options.classes.list.a + '">';
                html += '<img src="' + image.src[options.size] + '"  alt="' + escapeString(image.description) + '" title="' + escapeString(image.description) + '"  class="' + options.classes.list.img + '"/>';
                html += '</a>';
                html += '</li>';

                $ul.append(html);
            });

            if (options.link) {
                var html = '';
                html += '<p class="' + options.classes.link.p + '">';
                html += '<a href="' + instagramUrl + '" rel="' + rel + '" target="' + options.target + '" class="' + options.classes.link.a + '">';
                html += options.link;
                html += '</a>';
                html += '</p>';

                $ul.after(html);
            }
        }

        return true;
    };

})(jQuery);
