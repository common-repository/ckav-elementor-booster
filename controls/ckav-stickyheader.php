<?php
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Element_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class for sticky fuction
 * @since 1.0.0
 */
class Ckav_Stickyheader {

    /**
     * Init function
     * @since 1.0.0
     */
    public static function init() {
		
		add_action( 'elementor/element/section/section_effects/after_section_end',  [ __CLASS__, 'ckav_booster_get_controls' ] );

		// Commmon controles to hide elements
		add_action( 'elementor/element/common/_section_style/before_section_end',  [ __CLASS__, 'ckav_sticky_ctrl' ] );
		add_action( 'elementor/element/column/section_advanced/before_section_end',  [ __CLASS__, 'ckav_sticky_ctrl' ] );
		
		add_action( 'elementor/element/after_add_attributes',  [ __CLASS__, 'add_attributes' ] );

	}
	
	public static function add_attributes( $element ) {

		// Common setting for ckav sticky for all elements
		$settings = $element->get_settings_for_display();
		
		if (isset($settings[ '_ckav_sticky_hide' ]) && $settings[ '_ckav_sticky_hide' ] == 'yes') {
			$element->add_render_attribute( '_wrapper', [ 'class' => 'ckav-hide' ] );
		}

		// Section setting for ckav sticky
		if ( !in_array( $element->get_name(), [ 'section' ] ) ) return;

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) return;

		
		if ( isset($settings[ '_ckav_sticky_status' ]) && $settings[ '_ckav_sticky_status' ] == 'yes' ) {

			if (isset($settings[ '_ckav_sticky_hide' ]) && $settings[ '_ckav_sticky_hide' ] == 'yes') {
				$element->add_render_attribute( '_wrapper', [ 'class' => 'ckav-hide' ] );
			}
			
			if (isset($settings[ '_ckav_sticky_show_onsticky' ]) && $settings[ '_ckav_sticky_show_onsticky' ] == 'yes') {
				$element->add_render_attribute( '_wrapper', [ 'class' => 'hide-header' ] );
			}

			if (isset($settings[ '_ckav_sticky_afterscroll' ]) && $settings[ '_ckav_sticky_afterscroll' ] != 'yes') {
				$element->add_render_attribute( '_wrapper', [ 'class' => 'ckav-fixed-header' ] );
			}
			
		}

	}
	
	public static function ckav_sticky_ctrl( Element_Base $element ) {
		
		$element->add_control(
			'_ckav_sticky_title', 
			[
				'label'     => 'CKAV - STICKY HEADER SETTINGS',
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$element->add_control(
			'_ckav_sticky_hide',
			[
				'label' => __( 'Hide on sticky?', 'ckav-booster' ),
				'description' => __( 'Element will hide when sticky effect apply on scroll', 'ckav-booster' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'ckav-booster' ),
				'label_off' => __( 'No', 'ckav-booster' ),
				'return_value' => 'yes',
				'default' => 'no',
				'frontend_available' => true,
			]
		);
	}

	public static function ckav_booster_get_controls( Element_Base $element ) {
		
		self::booster_controls ( $element );

	}
	
	public static function booster_controls ( $el, $prefix = '' ) {
		
		$el->start_controls_section(
			'_ckav_sticky_section',
			[
				'label' => __( 'CKAV - STICKY HEADER', 'ckav-booster' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

			$el->add_control(
				'_ckav_sticky_status',
				[
					'label' => __( 'Enable settings?', 'ckav-booster' ),
					'description' => __( 'All setting effect will not display in editor mode', 'ckav-booster' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'ckav-booster' ),
					'label_off' => __( 'No', 'ckav-booster' ),
					'return_value' => 'yes',
					'default' => 'no',
					'frontend_available' => true
				]
			);

			$el->add_control(
				'_ckav_sticky_device',
				[
					'label' => __( 'Enable On', 'ckav-booster' ),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => 'true',
					'default' => [ 'desktop', 'tablet', 'mobile' ],
					'options' => [
						'desktop' => __( 'Desktop', 'ckav-booster' ),
						'tablet' => __( 'Tablet', 'ckav-booster' ),
						'mobile' => __( 'Mobile', 'ckav-booster' ),
					],
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
					'render_type' => 'none',
					'description' => __( 'Enable / disable all settings on various devices', 'ckav-booster' ),
					'frontend_available' => true,
					'separator' => 'before',
				]
			);

			$el->add_control(
				'_ckav_sticky_afterscroll',
				[
					'label' => __( 'Sticky after scroll?', 'ckav-booster' ),
					'description' => __( 'By default header not sticky only apply sticky after scroll', 'ckav-booster' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'ckav-booster' ),
					'label_off' => __( 'No', 'ckav-booster' ),
					'return_value' => 'yes',
					'default' => 'no',
					'frontend_available' => true,
					'separator' => 'before',
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			);

			$el->add_control(
				'_ckav_sticky_show_onsticky',
				[
					'label' => __( 'Show on sticky?', 'ckav-booster' ),
					'description' => __( 'By default header hide and only display when sticky effect active', 'ckav-booster' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'ckav-booster' ),
					'label_off' => __( 'No', 'ckav-booster' ),
					'return_value' => 'yes',
					'default' => 'no',
					'frontend_available' => true,
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			);

			/* $el->add_control(
				'_ckav_hide_scrolldown',
				[
					'label' => __( 'Hide on scroll down?', 'ckav-booster' ),
					'description' => __( 'Hide header on scroll down and show when scrolling up', 'ckav-booster' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'ckav-booster' ),
					'label_off' => __( 'No', 'ckav-booster' ),
					'return_value' => 'yes',
					'default' => 'no',
					'frontend_available' => true,
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			); */

			$el->add_control( 'hr', [ 
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'_ckav_sticky_status' => 'yes',
				],
			] );
	

			// Tabs start ==========
			$el->start_controls_tabs('style_tabs');

				// Before scroll =================
				$el->start_controls_tab(
					'_ckav_tab_before',
					[
						'label' => __( 'Before scroll', 'plugin-name' ),
						'condition' => [
							'_ckav_sticky_status' => 'yes',
						],
					]
				);
		
					$el->add_control(
						'_ckav_sticky_color',
						[
							'label' => __( 'Background color', 'ckav-booster' ),
							'description' => __( 'Background color will overwrite default section background when sticky effect active', 'ckav-booster' ),
							'type' => Controls_Manager::COLOR,
							'separator' => 'after',
							'selectors' => [
								'{{WRAPPER}}.ckav-stickyheader.before-scroll' => 'background-color: {{VALUE}}',
							],
							'condition' => [
								'_ckav_sticky_status' => 'yes',
							],
						]
					);

				$el->end_controls_tab();


				// After scroll =================
				$el->start_controls_tab(
					'_ckav_tab_after',
					[
						'label' => __( 'After scroll', 'plugin-name' ),
						'condition' => [
							'_ckav_sticky_status' => 'yes',
						],
					]
				);
		
					$el->add_control(
						'_ckav_sticky_color_after',
						[
							'label' => __( 'Background color', 'ckav-booster' ),
							'description' => __( 'Background color will overwrite default section background when sticky effect active', 'ckav-booster' ),
							'type' => Controls_Manager::COLOR,
							'separator' => 'after',
							'selectors' => [
								'{{WRAPPER}}.ckav-stickyheader.after-scroll' => 'background-color: {{VALUE}}',
							],
							'condition' => [
								'_ckav_sticky_status' => 'yes',
							],
						]
					);
		
				$el->end_controls_tab();
			
			$el->end_controls_tabs();
			// Tabs end ==========


			$el->add_control(
				'_ckav_sticky_top',
				[
					'label' => __( 'Top scroll distance', 'ckav-booster' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 5,
							'max' => 1000,
							'step' => 1,
						],
					],
					'default' => [
						'unit' => 'px',
						'size' => 50,
					],
					'frontend_available' => true,
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			);

			$el->add_responsive_control(
				'_ckav_sticky_padding',
				[
					'label' => __( 'Padding', 'ckav-booster' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}}.ckav-stickyheader.ckav-fixed-header:not(.before-scroll)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			);
			$el->add_responsive_control(
				'_ckav_sticky_maring',
				[
					'label' => __( 'Margin', 'ckav-booster' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}}.ckav-stickyheader.ckav-fixed-header:not(.before-scroll)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			);

			$el->add_responsive_control(
				'_ckav_sticky_height',
				[
					'label' => __( 'Height', 'ckav-booster' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'vh' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}}.ckav-stickyheader.ckav-fixed-header:not(.before-scroll) > .elementor-container' => 'min-height: {{size}}{{UNIT}};',
					],
					'condition' => [
						'_ckav_sticky_status' => 'yes',
					],
				]
			);

		$el->end_controls_section();
	}


}

