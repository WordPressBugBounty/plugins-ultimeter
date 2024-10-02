( function() {
    const { __ } = wp.i18n;
    const { registerBlockType } = wp.blocks;
    const el = wp.element.createElement;
    const { withSelect } = wp.data;
    const { SelectControl, ServerSideRender, Placeholder } = wp.components;
    var dispatch = wp.data.dispatch;
    dispatch( 'core' ).addEntities( [
        {
            name: 'ultimeters',           // route name
            kind: 'ultimeter/v1', // namespace
            baseURL: '/ultimeter/v1/ultimeters' // API path without /wp-json
        }
    ]);
    // Plugin logo    
    const iconEl = el('img', 
        { 
            src: ultimeterGlobal.logoUrl, 
            width: 150, 
            height: 150, 
            className: 'ultimeter-logo',
        },
     );

     const icon_small = el('img', 
        { 
            src: ultimeterGlobal.logoUrl, 
            width: 50, 
            height: 50,
        },
     );


    registerBlockType( 'ultimeter-gutenberg-block/shortcode-gutenberg', {
        title: __( 'Ultimeter', 'ultimeter' ),
        icon: icon_small,
        category: 'common',
        attributes: {
            id: { 
                    type: 'string' 
                },        
        },
        edit: withSelect( function( select, props ) {
            return {
                posts: select( 'core' ).getEntityRecords( 'ultimeter/v1', 'ultimeters', { per_page: -1 } ),
                metaValue: select( 'core/editor' ),
            }
        } ) (function(props) {
            
            function onChangeUltimeter( id ) {
                props.setAttributes( { id: id } );
            }

            var options = [];
            if( props.posts ) {
                options.push( { value: 0, label: __( 'Select an Ultimeter', 'ultimeter' ) } );
                props.posts.forEach((ultimeter) => { 
                    options.push({value:ultimeter.id, label:ultimeter.title});
                }); 
            } else {
                options.push( { value: 0, label: 'Loading...' } )
            }

            return [
                    el(
                        'div',
                        {
                          className:'placeholder-ultimeter-gutenberg-block',
                        },
                        el(
                            Placeholder,
                            {
                              label: __( 'Ultimeter', 'ultimeter' ),
                              icon:iconEl,
                            },
                            el(
                                SelectControl,
                                 {
                                    label: __( '', 'ultimeter' ),
                                    value: props.attributes.id,
                                    onChange: onChangeUltimeter,
                                    options:options,
                                }
                            )
                        ),
                    ),
            ];

        }),
        save: function(props) {
           return [
               el( ServerSideRender, {
                    block: 'ultimeter-gutenberg-block/shortcode-gutenberg',
                    attributes: props.attributes,
                } ) 
           ];
        },
    } );
}() );