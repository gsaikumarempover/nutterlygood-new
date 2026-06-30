// Import block modules
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { InspectorControls } from '@wordpress/block-editor';
import { BaseControl, SelectControl, Spinner } from '@wordpress/components';

class AddToWishlistBlockEdit extends Component {

	constructor() {
		super( ...arguments );

		this.state = {
			elementLoading: false,
			blockHTMLContent: '',
		};
	}

	componentDidMount() {
		const {
			attributes,
		} = this.props;

		const {
			item_id,
			button_behavior,
			button_type,
			show_count,
		} = attributes;

		this.setState( {
			elementLoading: true,
		} );

		apiFetch(
			{
				method: 'POST',
				path: '/qode-wishlist-for-woocommerce/v1/render-add-to-wishlist',
				data: {
					item_id,
					button_behavior,
					button_type,
					show_count,
				},
			}
		).then(
			response =>
			{
				this.setState( {
					elementLoading: false,
				} );

				if ( 'success' === response.status ) {
					this.setState( {
						blockHTMLContent: response.data,
					} );
				}
			}
		);
	}

	componentDidUpdate( prevProps ) {
		const {
			attributes,
		} = this.props;

		const {
			item_id,
			button_behavior,
			button_type,
			show_count,
		} = attributes;

		if (
			prevProps.attributes.item_id !== item_id ||
			prevProps.attributes.button_behavior !== button_behavior ||
			prevProps.attributes.button_type !== button_type ||
			prevProps.attributes.show_count !== show_count
		) {
			this.setState( {
				elementLoading: true,
			} );

			apiFetch(
				{
					method: 'POST',
					path: '/qode-wishlist-for-woocommerce/v1/render-add-to-wishlist',
					data: {
						item_id,
						button_behavior,
						button_type,
						show_count,
					},
				}
			).then(
				response =>
				{
					this.setState( {
						elementLoading: false,
					} );

					if ( 'success' === response.status ) {
						this.setState( {
							blockHTMLContent: response.data,
						} );
					}
				}
			);
		}
	}

	render() {
		const stateInstance = { ...this.state };

		const {
			attributes,
			setAttributes,
		} = this.props;

		const {
			item_id,
			button_behavior,
			button_type,
			show_count,
		} = attributes;

		let blockHTMLContent = stateInstance.blockHTMLContent;

		const productList      = window.qodeWishlistForWooCommerceAdminGlobal.product_list ?? [];
		let productListOptions = [
			{ value: '', label: __( '--Choose Product--', 'qode-wishlist-for-woocommerce' ) }
		];

		if ( productList ) {
			for ( const key in productList ) {
				productListOptions.push(
					{ value: key, label: productList[key] }
				);
			}
		}

		return (
			<>
				<InspectorControls>
					<BaseControl className='qode-woocommerce-base-control-container'>
						<SelectControl
							label={ __( 'Choose Product', 'qode-wishlist-for-woocommerce' ) }
							value={ item_id }
							options={ productListOptions }
							onChange={value => setAttributes( { item_id: parseInt( value, 10 ) } )}
						/>
						<SelectControl
							label={ __( '"Add to Wishlist" Behavior', 'qode-wishlist-for-woocommerce' ) }
							value={ button_behavior }
							options={ [
								{ value: '', label: __( 'Default', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'add', label: __( 'Show "Add to wishlist" button', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'view', label: __( 'Show "Browse wishlist" link', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'remove', label: __( 'Show "Remove from list" link', 'qode-wishlist-for-woocommerce' ) },
							] }
							onChange={ button_behavior => setAttributes( { button_behavior } )}
						/>
						<SelectControl
							label={ __( '"Add to Wishlist" Type', 'qode-wishlist-for-woocommerce' ) }
							value={ button_type }
							options={ [
								{ value: '', label: __( 'Default', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'icon-with-text', label: __( 'Icon with Text', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'icon', label: __( 'Only Icon', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'icon-with-tooltip', label: __( 'Icon with Tooltip', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'text', label: __( 'Only Text', 'qode-wishlist-for-woocommerce' ) },
							] }
							onChange={ button_type => setAttributes( { button_type } )}
						/>
						<SelectControl
							label={ __( 'Show a Wishlists Count', 'qode-wishlist-for-woocommerce' ) }
							help={ __( 'Show a counter that allows your customers to know how many times the product has been added to a wishlist', 'qode-wishlist-for-woocommerce' ) }
							value={ show_count }
							options={ [
								{ value: '', label: __( 'Default', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'no', label: __( 'No', 'qode-wishlist-for-woocommerce' ) },
								{ value: 'yes', label: __( 'Yes', 'qode-wishlist-for-woocommerce' ) },
							] }
							onChange={ show_count => setAttributes( { show_count } )}
						/>
					</BaseControl>
				</InspectorControls>
				{
					stateInstance.elementLoading
					?
					<div className="qode-woocommerce-block">
						<Spinner />
					</div>
					:
					<div
						className="qode-woocommerce-block qwfw--add-to-wishlist"
						dangerouslySetInnerHTML={{
							__html: stateInstance.blockHTMLContent ? blockHTMLContent : __('Please choose an Product', 'qode-wishlist-for-woocommerce')
						}}
					/>
				}
			</>
		)
	}
}

export default AddToWishlistBlockEdit;
