(function(blocks, editor, components, i18n, element) {

    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var RichText = wp.editor.RichText;
    var BlockControls = wp.editor.BlockControls;
    var AlignmentToolbar = wp.editor.AlignmentToolbar;
    var MediaUpload = wp.editor.MediaUpload;
    var InspectorControls = wp.editor.InspectorControls;
    var TextControl = components.TextControl;

    registerBlockType('jmacomp-list/block', { // The name of our block. Must be a string with prefix. Example: my-plugin/my-custom-block.
        title: i18n.__('Tabbed and Accordion Content'), // The title of our block.
        description: i18n.__('A custom block for displaying tabbed and accordion content.'), // The description of our block.
        icon: 'list-view', // Dashicon icon for our block. Custom icons can be added using inline SVGs.
        category: 'common', // The category of the block.

        edit: function(props) {
            var attributes = props.attributes;

            var id = props.attributes.id;

            var ServerSideRender = wp.components.ServerSideRender;

            return [
                el(BlockControls, {
                    key: 'controls'
                }),
                el(InspectorControls, {
                        key: 'inspector'
                    }, // Display the block options in the inspector panel.
                    el(components.PanelBody, {
                            title: i18n.__('Tab and Acordion ID'),
                            className: 'jmacomp-values',
                            initialOpen: true
                        },
                        el('p', {}, i18n.__('The id that matches a tab or accordion component')),
                        // Video id text field option.
                        el(TextControl, {
                            type: 'text',
                            label: i18n.__('The Component ID'),
                            value: id,
                            onChange: function(newid) {
                                props.setAttributes({
                                    id: newid
                                });
                            }
                        })

                    )
                ),
                el(ServerSideRender, {
                    block: 'jmacomp-list/block',
                    attributes: props.attributes,
                })
            ];
        },

        save: function() {
            return null;
        },
    });

})(
    window.wp.blocks,
    window.wp.editor,
    window.wp.components,
    window.wp.i18n,
    window.wp.element
);