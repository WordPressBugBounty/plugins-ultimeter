( function() {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const el = wp.element.createElement;
    const { withSelect } = wp.data;
    const { SelectControl, Placeholder } = wp.components;
    const ServerSideRender = ( wp.serverSideRender && wp.serverSideRender.default ) ? wp.serverSideRender.default : wp.serverSideRender;

    if ( wp.data && wp.data.dispatch( 'core' ) && wp.data.dispatch( 'core' ).addEntities ) {
        wp.data.dispatch( 'core' ).addEntities( [
            {
                name: 'ultimeters',
                kind: 'ultimeter/v1',
                baseURL: '/ultimeter/v1/ultimeters'
            }
        ] );
    }

    const logoUrl = ( typeof ultimeterGlobal !== 'undefined' && ultimeterGlobal.logoUrl ) ? ultimeterGlobal.logoUrl : '';

    const iconEl = el( 'img', {
        src: logoUrl,
        width: 150,
        height: 150,
        className: 'ultimeter-logo',
        alt: '',
    } );

    const icon_small = el( 'img', {
        src: logoUrl,
        width: 50,
        height: 50,
        alt: '',
    } );

    registerBlockType( 'ultimeter-gutenberg-block/shortcode-gutenberg', {
        apiVersion: 2,
        title: __( 'Ultimeter', 'ultimeter' ),
        icon: icon_small,
        category: 'widgets',
        attributes: {
            id: {
                type: 'string',
                default: '',
            },
        },
        edit: withSelect( function( select ) {
            return {
                posts: select( 'core' ).getEntityRecords( 'ultimeter/v1', 'ultimeters', { per_page: -1 } ),
            };
        } )( function( props ) {
            function onChangeUltimeter( id ) {
                props.setAttributes( { id: id && '0' !== id ? String( id ) : '' } );
            }

            var options = [];
            if ( props.posts ) {
                options.push( { value: '', label: __( 'Select an Ultimeter', 'ultimeter' ) } );
                props.posts.forEach( function( ultimeter ) {
                    options.push( {
                        value: String( ultimeter.id ),
                        label: ultimeter.title,
                    } );
                } );
            } else {
                options.push( { value: '', label: __( 'Loading…', 'ultimeter' ) } );
            }

            var selectedId = props.attributes.id ? String( props.attributes.id ) : '';
            var preview = null;

            if ( selectedId && ServerSideRender ) {
                preview = el( ServerSideRender, {
                    block: 'ultimeter-gutenberg-block/shortcode-gutenberg',
                    attributes: { id: selectedId },
                } );
            }

            return el(
                'div',
                {
                    className: 'placeholder-ultimeter-gutenberg-block',
                },
                el(
                    Placeholder,
                    {
                        label: __( 'Ultimeter', 'ultimeter' ),
                        icon: iconEl,
                    },
                    el(
                        SelectControl,
                        {
                            label: __( 'Select an Ultimeter', 'ultimeter' ),
                            value: selectedId,
                            onChange: onChangeUltimeter,
                            options: options,
                        }
                    )
                ),
                preview
            );
        } ),
        save: function() {
            return null;
        },
    } );
}() );
