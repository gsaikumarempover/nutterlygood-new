// Import block modules
import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { InspectorControls } from '@wordpress/block-editor';
import { BaseControl, SelectControl, Spinner } from '@wordpress/components';

class QuickViewButtonBlockEdit extends Component {

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
			button_type,
		} = attributes;

		this.setState( {
			elementLoading: true,
		} );

		apiFetch(
			{
				method: 'POST',
				path: '/qode-quick-view-for-woocommerce/v1/render-quick-view-button',
				data: {
					item_id,
					button_type,
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
			button_type,
		} = attributes;

		if ( prevProps.attributes.item_id !== item_id || prevProps.attributes.button_type !== button_type ) {
			this.setState( {
				elementLoading: true,
			} );

			apiFetch(
				{
					method: 'POST',
					path: '/qode-quick-view-for-woocommerce/v1/render-quick-view-button',
					data: {
						item_id,
						button_type,
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
			button_type,
		} = attributes;

		let blockHTMLContent = stateInstance.blockHTMLContent;

		const productList      = window.qodeQuickViewForWooCommerceAdminGlobal.product_list ?? [];
		let productListOptions = [
			{ value: '', label: __( '--Choose Product--', 'qode-quick-view-for-woocommerce' ) }
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
							label={ __( 'Choose Product', 'qode-quick-view-for-woocommerce' ) }
							value={ item_id }
							options={ productListOptions }
							onChange={value => setAttributes( { item_id: parseInt( value, 10 ) } )}
						/>
						<SelectControl
							label={ __( 'Button Type', 'qode-quick-view-for-woocommerce' ) }
							value={ button_type }
							options={ [
								{ value: '', label: __( 'Default', 'qode-quick-view-for-woocommerce' ) },
								{ value: 'icon-with-text', label: __( 'Icon with Text', 'qode-quick-view-for-woocommerce' ) },
								{ value: 'icon', label: __( 'Only Icon', 'qode-quick-view-for-woocommerce' ) },
								{ value: 'text', label: __( 'Only Text', 'qi-blocks' ) },
							] }
							onChange={ button_type => setAttributes( { button_type } )}
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
						className="qode-woocommerce-block qqvfw--quick-view-button"
						dangerouslySetInnerHTML={{
							__html: stateInstance.blockHTMLContent ? blockHTMLContent : __('Please choose an Product', 'qode-quick-view-for-woocommerce')
						}}
					/>
				}
			</>
		)
	}
}

export default QuickViewButtonBlockEdit;
