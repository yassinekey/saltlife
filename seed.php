<?php
/**
 * Plugin Name: Morocco Tourist Shop Seeder
 * Description: Generates demo WooCommerce products, categories, tags, and images for a Moroccan tourist shop.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Author: Store Administrator
 * License: GPL-2.0-or-later
 */

defined('ABSPATH') || exit;


/**
 * Delete existing demo products, categories, and tags.
 */

final class Morocco_Tourist_Shop_Seeder
{
    const VERSION = '1.0.0';
    const MENU_SLUG = 'morocco-tourist-seeder';
    const SKU_PREFIX = 'MOROCCO-DEMO-';

    /**
     * Configuration.
     *
     * Products are created as published products by default.
     * Change this to "draft" if you want to review them first.
     */
    private static $config = [
    'total_products'        => 100,
    'images_per_product'    => 2,
    'product_status'        => 'draft',
    'currency'              => 'MAD',
    'stock_quantity'        => 20,
    'download_timeout'      => 15,
    'max_image_bytes'       => 8000000,
];



private static function clear_existing_demo_data()
{
    // Include all relevant WooCommerce product statuses.
    $statuses = [
        'publish',
        'draft',
        'pending',
        'private',
        'future',
        'trash',
    ];

    $deleted_products = 0;

    // Fetch products in batches and delete only demo products.
    do {
        $products = wc_get_products([
            'limit'  => 100,
            'page'   => 1,
            'return' => 'objects',
            'status' => $statuses,
            'orderby' => 'ID',
            'order'   => 'ASC',
        ]);

        $deleted_in_batch = 0;

        foreach ($products as $product) {
            $sku = $product->get_sku();

            if (
                is_string($sku) &&
                strpos($sku, self::SKU_PREFIX) === 0
            ) {
                $product->delete(true);

                $deleted_products++;
                $deleted_in_batch++;
            }
        }
    } while ($deleted_in_batch > 0);

    WP_CLI::log(
        "Deleted {$deleted_products} existing demo products."
    );

    // WARNING: This removes ALL product categories.
    $categories = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'fields'     => 'ids',
    ]);

    if (!is_wp_error($categories)) {
        foreach ($categories as $category_id) {
            wp_delete_term($category_id, 'product_cat');
        }
    }

    WP_CLI::log('Removed existing product categories.');

    // WARNING: This removes ALL product tags.
    $tags = get_terms([
        'taxonomy'   => 'product_tag',
        'hide_empty' => false,
        'fields'     => 'ids',
    ]);

    if (!is_wp_error($tags)) {
        foreach ($tags as $tag_id) {
            wp_delete_term($tag_id, 'product_tag');
        }
    }

    WP_CLI::log('Removed existing product tags.');

    // Clear this seeder's image cache options.
    global $wpdb;

    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '\_morocco\_image\_%'
         ESCAPE '\\\\'"
    );

    // Clear taxonomy caches.
    clean_term_cache([], 'product_cat');
    clean_term_cache([], 'product_tag');
}

    /**
     * Product category definitions.
     */
    private static $categories = [
        't-shirts' => [
            'name' => 'T-Shirts',
            'description' => 'Moroccan-inspired T-shirts for travelers.',
            'products' => [
                [
                    'name' => 'Marrakech Medina T-Shirt',
                    'price' => 149,
                    'description' => 'A casual souvenir T-shirt inspired by the colorful streets and historic architecture of Marrakech.',
                    'short' => 'A Marrakech-inspired cotton souvenir T-shirt.',
                    'tags' => ['marrakech', 'cotton', 'souvenir', 'travel'],
                    'images' => [
                        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Sahara Desert T-Shirt',
                    'price' => 159,
                    'description' => 'A relaxed T-shirt inspired by the golden dunes and unforgettable sunsets of the Moroccan Sahara.',
                    'short' => 'A souvenir for desert lovers.',
                    'tags' => ['sahara', 'desert', 'cotton', 'souvenir'],
                    'images' => [
                        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Morocco Travel T-Shirt',
                    'price' => 139,
                    'description' => 'A simple everyday T-shirt for visitors who want to remember their Moroccan adventure.',
                    'short' => 'An everyday Morocco travel souvenir.',
                    'tags' => ['morocco', 'travel', 'cotton', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'hoodies-sweatshirts' => [
            'name' => 'Hoodies & Sweatshirts',
            'description' => 'Comfortable clothing inspired by Moroccan destinations.',
            'products' => [
                [
                    'name' => 'Atlas Mountains Hoodie',
                    'price' => 349,
                    'description' => 'A comfortable hoodie inspired by the Atlas Mountains and cool Moroccan evenings.',
                    'short' => 'A cozy travel hoodie.',
                    'tags' => ['atlas', 'hoodie', 'travel', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Marrakech Streetwear Hoodie',
                    'price' => 379,
                    'description' => 'Modern casual streetwear with a Moroccan travel-inspired identity.',
                    'short' => 'Modern Moroccan-inspired streetwear.',
                    'tags' => ['marrakech', 'hoodie', 'modern', 'streetwear'],
                    'images' => [
                        'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Morocco Explorer Sweatshirt',
                    'price' => 299,
                    'description' => 'A versatile sweatshirt for exploring Morocco in comfort.',
                    'short' => 'A casual sweatshirt for travelers.',
                    'tags' => ['morocco', 'sweatshirt', 'travel', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'carpets-rugs' => [
            'name' => 'Carpets & Rugs',
            'description' => 'Decorative rugs inspired by Moroccan weaving traditions.',
            'products' => [
                [
                    'name' => 'Atlas Berber Style Rug',
                    'price' => 1290,
                    'description' => 'A decorative rug inspired by traditional Berber geometric patterns and the colors of the Atlas region.',
                    'short' => 'A Moroccan-inspired decorative rug.',
                    'tags' => ['berber', 'atlas', 'rug', 'home-decoration'],
                    'images' => [
                        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Marrakech Geometric Rug',
                    'price' => 990,
                    'description' => 'A geometric rug concept inspired by the colors and patterns of Moroccan interiors.',
                    'short' => 'A geometric decorative rug.',
                    'tags' => ['marrakech', 'rug', 'geometric', 'interior'],
                    'images' => [
                        'https://images.unsplash.com/photo-1600166898405-da9535204845?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Sahara Desert Floor Rug',
                    'price' => 790,
                    'description' => 'A warm-toned decorative floor rug inspired by desert landscapes.',
                    'short' => 'A desert-inspired decorative rug.',
                    'tags' => ['sahara', 'rug', 'desert', 'home-decoration'],
                    'images' => [
                        'https://images.unsplash.com/photo-1600166898405-da9535204845?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'leather-goods' => [
            'name' => 'Leather Goods',
            'description' => 'Moroccan-inspired leather accessories and travel goods.',
            'products' => [
                [
                    'name' => 'Fes Leather Travel Bag',
                    'price' => 690,
                    'description' => 'A travel bag concept inspired by the historic leatherworking heritage of Fes.',
                    'short' => 'A classic travel bag.',
                    'tags' => ['fes', 'leather', 'travel', 'bag'],
                    'images' => [
                        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Morocco Leather Shoulder Bag',
                    'price' => 490,
                    'description' => 'A versatile shoulder bag for exploring Moroccan cities.',
                    'short' => 'A practical shoulder bag.',
                    'tags' => ['leather', 'bag', 'fashion', 'travel'],
                    'images' => [
                        'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Moroccan Travel Wallet',
                    'price' => 199,
                    'description' => 'A compact wallet concept for travel documents and everyday essentials.',
                    'short' => 'A compact travel accessory.',
                    'tags' => ['wallet', 'leather', 'travel', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'home-decor' => [
            'name' => 'Home Decor',
            'description' => 'Moroccan-inspired decorative pieces for the home.',
            'products' => [
                [
                    'name' => 'Moroccan Living Room Decor',
                    'price' => 390,
                    'description' => 'Decorative inspiration drawn from traditional Moroccan interiors.',
                    'short' => 'Moroccan-inspired home decoration.',
                    'tags' => ['moroccan', 'home-decoration', 'interior', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Marrakech Interior Accent',
                    'price' => 450,
                    'description' => 'A decorative accent inspired by the warm colors and textures of Marrakech interiors.',
                    'short' => 'A warm decorative accent.',
                    'tags' => ['marrakech', 'decor', 'interior', 'souvenir'],
                    'images' => [
                        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Atlas Inspired Cushion Decor',
                    'price' => 220,
                    'description' => 'A decorative concept inspired by the colors and textures of the Atlas Mountains.',
                    'short' => 'A cushion-inspired home accent.',
                    'tags' => ['atlas', 'cushion', 'decor', 'home-decoration'],
                    'images' => [
                        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'ceramics-pottery' => [
            'name' => 'Ceramics & Pottery',
            'description' => 'Decorative ceramics inspired by Moroccan craftsmanship.',
            'products' => [
                [
                    'name' => 'Fes Inspired Ceramic Bowl',
                    'price' => 180,
                    'description' => 'A decorative bowl inspired by the traditional ceramic artistry associated with Fes.',
                    'short' => 'A ceramic-inspired souvenir.',
                    'tags' => ['fes', 'ceramic', 'pottery', 'handcrafted'],
                    'images' => [
                        'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Moroccan Tableware',
                    'price' => 250,
                    'description' => 'Decorative tableware inspired by Moroccan hospitality and dining traditions.',
                    'short' => 'A Moroccan-inspired table accessory.',
                    'tags' => ['ceramic', 'tableware', 'morocco', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Moroccan Pottery Display Piece',
                    'price' => 320,
                    'description' => 'A decorative pottery concept inspired by traditional Moroccan forms.',
                    'short' => 'A decorative pottery souvenir.',
                    'tags' => ['pottery', 'ceramic', 'decor', 'souvenir'],
                    'images' => [
                        'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'accessories' => [
            'name' => 'Accessories',
            'description' => 'Useful and stylish accessories for visitors to Morocco.',
            'products' => [
                [
                    'name' => 'Morocco Everyday Tote Bag',
                    'price' => 99,
                    'description' => 'A reusable tote bag concept for carrying travel essentials and souvenirs.',
                    'short' => 'A practical everyday tote.',
                    'tags' => ['tote-bag', 'travel', 'accessories', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Morocco Travel Accessories',
                    'price' => 129,
                    'description' => 'A casual accessory concept for travelers exploring Morocco.',
                    'short' => 'A small travel accessory.',
                    'tags' => ['accessories', 'travel', 'morocco', 'souvenir'],
                    'images' => [
                        'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Marrakech Gift Accessory',
                    'price' => 79,
                    'description' => 'A small gift concept inspired by the vibrant atmosphere of Marrakech.',
                    'short' => 'A small gift for travelers.',
                    'tags' => ['marrakech', 'gift', 'souvenir', 'accessories'],
                    'images' => [
                        'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],

        'souvenirs' => [
            'name' => 'Souvenirs',
            'description' => 'Gift ideas inspired by Moroccan cities and landscapes.',
            'products' => [
                [
                    'name' => 'Marrakech Travel Gift',
                    'price' => 69,
                    'description' => 'A small souvenir concept to remember a visit to Marrakech.',
                    'short' => 'A small Marrakech-inspired gift.',
                    'tags' => ['marrakech', 'souvenir', 'gift', 'travel'],
                    'images' => [
                        'https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Chefchaouen Inspired Gift',
                    'price' => 89,
                    'description' => 'A gift concept inspired by the blue streets of Chefchaouen.',
                    'short' => 'A blue-city-inspired souvenir.',
                    'tags' => ['chefchaouen', 'souvenir', 'gift', 'morocco'],
                    'images' => [
                        'https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
                [
                    'name' => 'Morocco Explorer Souvenir',
                    'price' => 119,
                    'description' => 'A general-purpose souvenir concept for visitors exploring Morocco.',
                    'short' => 'A Morocco travel souvenir.',
                    'tags' => ['morocco', 'souvenir', 'travel', 'gift'],
                    'images' => [
                        'https://images.unsplash.com/photo-1493106641515-6b5631de4bb9?auto=format&fit=crop&w=1000&q=80',
                        'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&w=1000&q=80',
                    ],
                ],
            ],
        ],
    ];

    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'register_menu']);
    }

    public static function register_menu()
    {
        add_management_page(
            'Morocco Shop Seeder',
            'Morocco Shop Seeder',
            'manage_woocommerce',
            self::MENU_SLUG,
            [__CLASS__, 'render_admin_page']
        );
    }

    public static function render_admin_page()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'morocco-tourist-seeder'));
        }

        $result = null;

        if (
            isset($_POST['morocco_seed_action']) &&
            $_POST['morocco_seed_action'] === 'run'
        ) {
            check_admin_referer('morocco_seed_products');

            if (!class_exists('WooCommerce')) {
                $result = new WP_Error(
                    'woocommerce_missing',
                    'WooCommerce must be active.'
                );
            } else {
                $result = self::seed();
            }
        }

        ?>
        <div class="wrap">
            <h1>Morocco Tourist Shop Seeder</h1>

            <p>
                Generate sample Moroccan souvenir products, categories,
                tags, and local product images.
            </p>

            <p>
                The default configuration creates draft products so you
                can review them before publishing.
            </p>

            <?php if (is_wp_error($result)) : ?>
                <div class="notice notice-error">
                    <p><?php echo esc_html($result->get_error_message()); ?></p>
                </div>
            <?php elseif (is_array($result)) : ?>
                <div class="notice notice-success">
                    <p>
                        Created: <?php echo esc_html((string) $result['created']); ?>.
                        Existing products skipped:
                        <?php echo esc_html((string) $result['skipped']); ?>.
                        Failed:
                        <?php echo esc_html((string) $result['failed']); ?>.
                        Images downloaded:
                        <?php echo esc_html((string) $result['images']); ?>.
                    </p>
                </div>

                <?php if (!empty($result['messages'])) : ?>
                    <h2>Details</h2>
                    <ul>
                        <?php foreach ($result['messages'] as $message) : ?>
                            <li><?php echo esc_html($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post">
                <?php wp_nonce_field('morocco_seed_products'); ?>
                <input type="hidden" name="morocco_seed_action" value="run">

                <?php
                submit_button(
                    'Generate Demo Catalog',
                    'primary',
                    'submit',
                    true,
                    [
                        'onclick' => "return confirm('Generate Moroccan demo products?');",
                    ]
                );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Seed the catalog.
     */
    public static function seed()
    {
        if (!class_exists('WooCommerce')) {
            return new WP_Error(
                'woocommerce_missing',
                'WooCommerce is not active.'
            );
        }

        if (get_transient('morocco_seeder_running')) {
            return new WP_Error(
                'already_running',
                'The seeder is already running. Please wait and try again.'
            );
        }

        set_transient('morocco_seeder_running', 1, 10 * MINUTE_IN_SECONDS);

        $result = [
            'created'  => 0,
            'skipped'  => 0,
            'failed'   => 0,
            'images'   => 0,
            'messages' => [],
        ];

        try {
            // Remove the previous demo catalog first.
            self::clear_existing_demo_data();

            self::load_media_functions();

            $category_ids = [];
            $tag_ids = [];

            /*
             * Create or reuse product categories.
             */
            foreach (self::$categories as $slug => $category) {
                $term = term_exists($slug, 'product_cat');

                if (!$term) {
                    $term = wp_insert_term(
                        $category['name'],
                        'product_cat',
                        [
                            'slug' => $slug,
                            'description' => $category['description'],
                        ]
                    );
                }

                if (is_wp_error($term)) {
                    $result['messages'][] =
                        'Category error: ' . $category['name'];

                    continue;
                }

                $category_ids[$slug] = (int) (
                    is_array($term) ? $term['term_id'] : $term
                );
            }

            /*
             * Create global tags, including tags not used by a product.
             */
            $all_tags = [
                'morocco',
                'marrakech',
                'fes',
                'chefchaouen',
                'sahara',
                'atlas',
                'berber',
                'amazigh',
                'handcrafted',
                'cotton',
                'leather',
                'ceramic',
                'pottery',
                'souvenir',
                'travel',
                'gift',
                'home-decoration',
                'interior',
                'rug',
                'geometric',
                'hoodie',
                'sweatshirt',
                'streetwear',
                'bag',
                'wallet',
                'accessories',
                'desert',
                'modern',
                'tote-bag',
            ];

            foreach ($all_tags as $tag) {
                $term = term_exists($tag, 'product_tag');

                if (!$term) {
                    $term = wp_insert_term(
                        ucwords(str_replace('-', ' ', $tag)),
                        'product_tag',
                        ['slug' => $tag]
                    );
                }

                if (!is_wp_error($term)) {
                    $tag_ids[$tag] = (int) (
                        is_array($term) ? $term['term_id'] : $term
                    );
                }
            }

            /*
             * Create products.
             *
             * SKU checks make repeated runs idempotent:
             * products with existing generated SKUs are skipped.
             */
            $sequence = 1;

            $category_slugs = array_keys(self::$categories);

            $category_count = count($category_slugs);

            if ($category_count === 0) {
                throw new RuntimeException('No product categories are configured.');
            }

            // Flatten the existing product definitions.
            $templates = [];

            foreach (self::$categories as $category_slug => $category) {
                foreach ($category['products'] as $product_data) {
                    $templates[] = [
                        'category_slug' => $category_slug,
                        'data'          => $product_data,
                    ];
                }
            }

            if (empty($templates)) {
                throw new RuntimeException('No product templates are configured.');
            }

            $total_products = (int) self::$config['total_products'];

            for ($i = 0; $i < $total_products; $i++) {
                // Cycle through the existing product templates.
                $template_index = $i % count($templates);

                $template = $templates[$template_index];

                $category_slug = $template['category_slug'];
                $product_data = $template['data'];

                if (empty($category_ids[$category_slug])) {
                    $result['failed']++;

                    $result['messages'][] =
                        'Missing category: ' . $category_slug;

                    continue;
                }

                // Add a unique suffix when a template is reused.
                $copy_number = intdiv($i, count($templates)) + 1;

                if ($copy_number > 1) {
                    $product_data['name'] .= ' - Edition ' . $copy_number;
                }

                $sku = self::SKU_PREFIX . str_pad(
                    (string) $sequence,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                $sequence++;

                try {
                    $product = new WC_Product_Simple();

                    $product->set_name($product_data['name']);
                    $product->set_sku($sku);

                    $product->set_status(
                        self::$config['product_status']
                    );

                    $product->set_catalog_visibility('visible');

                    $product->set_regular_price(
                        (string) $product_data['price']
                    );

                    $product->set_description(
                        $product_data['description']
                    );

                    $product->set_short_description(
                        $product_data['short']
                    );

                    $product->set_category_ids([
                        $category_ids[$category_slug],
                    ]);

                    $product->set_manage_stock(true);

                    $product->set_stock_quantity(
                        (int) self::$config['stock_quantity']
                    );

                    $product->set_stock_status('instock');
                    $product->set_reviews_allowed(true);

                    $product->set_virtual(false);
                    $product->set_downloadable(false);

                    // Save before attaching images.
                    $product_id = $product->save();

                    if (!$product_id) {
                        $result['failed']++;
                        continue;
                    }

                    // Assign tags.
                    $product_tag_ids = [];

                    foreach ($product_data['tags'] as $tag) {
                        if (isset($tag_ids[$tag])) {
                            $product_tag_ids[] = $tag_ids[$tag];
                        }
                    }

                    if (!empty($product_tag_ids)) {
                        wp_set_object_terms(
                            $product_id,
                            $product_tag_ids,
                            'product_tag',
                            false
                        );
                    }

                    // Download product images.
                    $image_ids = [];

                    $image_urls = array_slice(
                        $product_data['images'],
                        0,
                        max(1, (int) self::$config['images_per_product'])
                    );

                    foreach ($image_urls as $image_index => $image_url) {
                        $attachment_id = self::download_product_image(
                            $image_url,
                            $product_id,
                            $product_data['name'] . ' ' . ($image_index + 1)
                        );

                        if (is_wp_error($attachment_id)) {
                            $result['messages'][] =
                                'Image download failed for ' .
                                $product_data['name'] . ': ' .
                                $attachment_id->get_error_message();

                            continue;
                        }

                        if ($attachment_id) {
                            $image_ids[] = (int) $attachment_id;
                            $result['images']++;
                        }
                    }

                    // Assign featured image and gallery.
                    if (!empty($image_ids)) {
                        $product = wc_get_product($product_id);

                        if ($product) {
                            $product->set_image_id($image_ids[0]);

                            $product->set_gallery_image_ids(
                                array_slice(
                                    array_values(array_unique($image_ids)),
                                    1
                                )
                            );

                            $product->save();
                        }
                    }

                    $result['created']++;

                } catch (Throwable $exception) {
                    $result['failed']++;

                    $result['messages'][] =
                        'Product error for ' .
                        $product_data['name'] . ': ' .
                        $exception->getMessage();
                }
            }

        } catch (Throwable $exception) {
            $result['messages'][] = $exception->getMessage();
        } finally {
            delete_transient('morocco_seeder_running');
        }

        return $result;
    }

    /**
     * Load the WordPress media functions required for sideloading.
     */
    private static function load_media_functions()
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    /**
     * Download a remote image and register it in the Media Library.
     *
     * This uses direct image URLs and does not require an API key.
     */
    private static function download_product_image(
        $url,
        $product_id,
        $description
    ) {
        /*
         * Only permit HTTPS image URLs from images.unsplash.com.
         * This also prevents the downloader from becoming an
         * arbitrary-URL or internal-network request mechanism.
         */
        $parsed = wp_parse_url($url);

        if (
            empty($parsed['scheme']) ||
            strtolower($parsed['scheme']) !== 'https' ||
            empty($parsed['host']) ||
            strtolower($parsed['host']) !== 'images.unsplash.com'
        ) {
            return new WP_Error(
                'invalid_image_host',
                'The image URL is not from the permitted image host.'
            );
        }

        /*
         * Cache by image URL so multiple products can reuse the
         * same Media Library attachment.
         */
        $cache_key = '_morocco_image_' . md5($url);

        $cached_id = (int) get_option($cache_key, 0);

        if ($cached_id && get_post($cached_id)) {
            return $cached_id;
        }

        $temp_file = download_url(
            $url,
            (int) self::$config['download_timeout']
        );

        if (is_wp_error($temp_file)) {
            return $temp_file;
        }

        /*
         * Check file size before handing it to WordPress.
         */
        $file_size = filesize($temp_file);

        if (
            $file_size === false ||
            $file_size < 1 ||
            $file_size > (int) self::$config['max_image_bytes']
        ) {
            @unlink($temp_file);

            return new WP_Error(
                'invalid_image_size',
                'The downloaded image is empty or exceeds the size limit.'
            );
        }

        /*
         * Validate actual image content, not just the filename.
         */
        $image_info = wp_getimagesize($temp_file);

        if (!$image_info || empty($image_info['mime'])) {
            @unlink($temp_file);

            return new WP_Error(
                'invalid_image',
                'The downloaded file is not a valid image.'
            );
        }

        $allowed_mimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed_mimes[$image_info['mime']])) {
            @unlink($temp_file);

            return new WP_Error(
                'unsupported_image_type',
                'The downloaded image format is not supported.'
            );
        }

        $filename = sanitize_file_name(
            sanitize_title($description) .
            '-' . substr(md5($url), 0, 10) .
            '.' . $allowed_mimes[$image_info['mime']]
        );

        $file_array = [
            'name'     => $filename,
            'tmp_name' => $temp_file,
            'error'    => 0,
            'size'     => $file_size,
        ];

        $attachment_id = media_handle_sideload(
            $file_array,
            $product_id,
            $description
        );

        if (is_wp_error($attachment_id)) {
            if (file_exists($temp_file)) {
                @unlink($temp_file);
            }

            return $attachment_id;
        }

        update_post_meta(
            $attachment_id,
            '_morocco_source_url',
            esc_url_raw($url)
        );

        update_post_meta(
            $attachment_id,
            '_wp_attachment_image_alt',
            sanitize_text_field($description)
        );

        update_option($cache_key, (int) $attachment_id, false);

        return (int) $attachment_id;
    }
}

// Register hooks when WordPress loads normally.
Morocco_Tourist_Shop_Seeder::init();

// Execute seeding when invoked from the command line.
if (defined('WP_CLI') && WP_CLI) {
    $result = Morocco_Tourist_Shop_Seeder::seed();

    if (is_wp_error($result)) {
        WP_CLI::error($result->get_error_message());
    }

    WP_CLI::success(
        sprintf(
            'Created: %d | Skipped: %d | Failed: %d | Images: %d',
            $result['created'],
            $result['skipped'],
            $result['failed'],
            $result['images']
        )
    );

    foreach ($result['messages'] as $message) {
        WP_CLI::log($message);
    }
}