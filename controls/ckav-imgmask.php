<?php
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Element_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class for image mask fuction
 * @since 1.0.0
 */
class Ckav_Imgmask {

    /**
     * Init function
     * @since 1.0.0
     */
    public static function init() {
		
		add_action( 'elementor/element/section/section_background_overlay/before_section_end',  [ __CLASS__, 'ckav_imgmask_get_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_background_overlay/before_section_end',  [ __CLASS__, 'ckav_imgmask_get_controls' ], 10, 2 );
		add_action( 'elementor/element/image/section_style_image/before_section_end',  [ __CLASS__, 'ckav_imgmask_get_controls' ], 10, 2 );
		add_action( 'elementor/element/spacer/section_spacer/before_section_end',  [ __CLASS__, 'ckav_imgmask_get_controls' ], 10, 2 );
		
		add_action( 'elementor/element/after_add_attributes',  [ __CLASS__, 'add_attributes' ] );

	}
	
	public static function add_attributes( $element ) {
		
		if ( !in_array( $element->get_name(), [ 'section', 'column', 'image', 'spacer' ] ) ) {
            return;
		}

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) return;

		$settings = $element->get_settings_for_display();
		
		if ( isset($settings[ '_ckav_booster_imgmask_status' ]) && $settings[ '_ckav_booster_imgmask_status' ] == 'yes' ) {
			$element->add_render_attribute( '_wrapper', [
				'class' => 'ckav-imgmask'
			] );
		}

	}
	
	public static function ckav_imgmask_get_controls( $element, $args ) {
		
		self::imgmask_controls ( $element );

	}

	public static function mask_size($type = '', $prefix = '', $device = '')
	{
		$mask_w = '{{_ckav_booster_imgmask_w'.$prefix.$device.'.SIZE}}{{_ckav_booster_imgmask_w'.$prefix.$device.'.UNIT}}';
		$mask_h = '{{_ckav_booster_imgmask_h'.$prefix.$device.'.SIZE}}{{_ckav_booster_imgmask_h'.$prefix.$device.'.UNIT}}';

		if ($type == 'width') {
			return $mask_size = '-webkit-mask-size: {{SIZE}}{{UNIT}} '.$mask_h.'; mask-size: {{SIZE}}{{UNIT}} '.$mask_h.';';
		} else {
			return $mask_size = '-webkit-mask-size: '.$mask_w.' {{SIZE}}{{UNIT}}; mask-size: '.$mask_w.' {{SIZE}}{{UNIT}};';
		}
	
	}
	
	public static function imgmask_controls ( $el, $prefix = '' ) {
		
		$css_selector = '';
		
		if ($el->get_name() == 'image') {
			$css_selector = '{{WRAPPER}}.ckav-imgmask .elementor-image img';
		}
		if ($el->get_name() == 'section') {
			$css_selector = '{{WRAPPER}}.ckav-imgmask > .elementor-background-overlay';
		}
		if ($el->get_name() == 'column') {
			$css_selector = '{{WRAPPER}}.ckav-imgmask > .elementor-column-wrap > .elementor-background-overlay';
		}
		if ($el->get_name() == 'spacer') {
			$css_selector = '{{WRAPPER}}.ckav-imgmask > .elementor-widget-container';
		}

		$el->add_control(
			'_ckav_booster_imgmask_title', 
			[
				'label'     => 'CKAV - IMAGE MASK',
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$el->add_control(
			'_ckav_booster_imgmask_status',
			[
				'label' => __( 'Apply image mask?', 'ckav-booster' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'ckav-booster' ),
				'label_off' => __( 'No', 'ckav-booster' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'after',
				'frontend_available' => true
			]
		);

		$el->add_responsive_control(
			'_ckav_booster_imgmask_img', 
			[
				'label'   => __( 'Choose Image', 'ckav-booster' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'selectors' => [
					$css_selector => '-webkit-mask-image: url("{{URL}}"); mask-image: url("{{URL}}"); -webkit-mask-mode: luminance; mask-mode: luminance; mask-clip: no-clip; -webkit-mask-clip: no-clip;',
				],
				'condition' => [
					'_ckav_booster_imgmask_status' => [ 'yes' ]
				],
			]
		);

		$el->add_responsive_control(
			'_ckav_booster_imgmask_position',
			[
				'label'   => __( 'Position', 'ckav-booster' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center left',
				'options' => [
					'center left'   => __( 'Center Left', 'ckav-booster' ),
					'center right'  => __( 'Center Right', 'ckav-booster' ),
					'center center' => __( 'Center Center', 'ckav-booster' ),
					'top center'    => __( 'Top Center', 'ckav-booster' ),
					'top left'      => __( 'Top Left', 'ckav-booster' ),
					'top right'     => __( 'Top Right', 'ckav-booster' ),
					'bottom center' => __( 'Bottom Center', 'ckav-booster' ),
					'bottom left'   => __( 'Bottom Left', 'ckav-booster' ),
					'bottom right'  => __( 'Bottom Right', 'ckav-booster' ),
				],
				'selectors' => [
					$css_selector => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
				],
				'condition' => [
					'_ckav_booster_imgmask_status' => [ 'yes' ]
				],
			]
		);

		$el->add_responsive_control(
			'_ckav_booster_imgmask_repeat',
			[
				'label'   => __( 'Repeat', 'ckav-booster' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => __( 'No-repeat', 'ckav-booster' ),
					'repeat'    => __( 'Repeat', 'ckav-booster' ),
					'repeat-x'  => __( 'Repeat-X', 'ckav-booster' ),
					'repeat-y'  => __( 'Repeat-Y', 'ckav-booster' ),
				],
				'selectors' => [
					$css_selector => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
				],
				'condition' => [
					'_ckav_booster_imgmask_status' => [ 'yes' ]
				],
			]
		);

		$el->add_responsive_control(
			'_ckav_booster_imgmask_size',
			[
				'label'   => __( 'Size', 'ckav-booster' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto'    => __( 'Auto', 'ckav-booster' ),
					'cover'   => __( 'Cover', 'ckav-booster' ),
					'contain' => __( 'Contain', 'ckav-booster' ),
					'custom'  => __( 'Custom', 'ckav-booster' ),
				],
				'selectors' => [
					$css_selector => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
				],
				'condition' => [
					'_ckav_booster_imgmask_status' => [ 'yes' ]
				],
			]
		);

		

		$el->add_responsive_control(
			'_ckav_booster_imgmask_w',
			[
				'label' => __( 'Width', 'ckav-booster' ),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					$css_selector => self::mask_size('width', $prefix, ''),
				],
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'selectors' => [
							$css_selector => self::mask_size('width', $prefix, '_tablet'),
						],
						'condition' => [
							'_ckav_booster_imgmask_size_tablet' => [ 'custom' ],
							'_ckav_booster_imgmask_status' => [ 'yes' ]
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'selectors' => [
							$css_selector => self::mask_size('width', $prefix, '_mobile'),
						],
						'condition' => [
							'_ckav_booster_imgmask_size_mobile' => [ 'custom' ],
							'_ckav_booster_imgmask_status' => [ 'yes' ]
						],
					],
				],
				'condition' => [
					'_ckav_booster_imgmask_size' => [ 'custom' ],
					'_ckav_booster_imgmask_status' => [ 'yes' ]
				],
			]
		);

		$el->add_responsive_control(
			'_ckav_booster_imgmask_h',
			[
				'label' => __( 'Height', 'ckav-booster' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					$css_selector => self::mask_size('h', $prefix, ''),
				],
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'selectors' => [
							$css_selector => self::mask_size('h', $prefix, '_tablet'),
						],
						'condition' => [
							'_ckav_booster_imgmask_size_tablet' => [ 'custom' ],
							'_ckav_booster_imgmask_status' => [ 'yes' ]
						],
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'selectors' => [
							$css_selector => self::mask_size('h', $prefix, '_mobile'),
						],
						'condition' => [
							'_ckav_booster_imgmask_size_mobile' => [ 'custom' ],
							'_ckav_booster_imgmask_status' => [ 'yes' ]
						],
					],
				],
				'condition' => [
					'_ckav_booster_imgmask_size' => [ 'custom' ],
					'_ckav_booster_imgmask_status' => [ 'yes' ]
				],
			]
		);
	}
}

