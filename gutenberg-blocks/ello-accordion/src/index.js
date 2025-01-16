(function (blocks, element, blockEditor, components) {
    var el = element.createElement;
    var RichText = blockEditor.RichText;
    var MediaUpload = blockEditor.MediaUpload;
    var Button = components.Button;

    blocks.registerBlockType('ello-accordion-block/main', {
        title: 'Accordion',
        icon: 'list-view',
        category: 'common',

        attributes: {
            items: {
                type: 'array',
                default: [{
                    title: '',
                    content: '',
                    imageUrl: '',
                    imageId: null,
                    imageAlt: ''
                }]
            }
        },

        edit: function (props) {
            var items = props.attributes.items || [];

            function onAddItem() {
                var newItems = [...items];
                newItems.push({
                    title: '',
                    content: '',
                    imageUrl: '',
                    imageId: null,
                    imageAlt: ''
                });
                props.setAttributes({ items: newItems });
            }

            function onRemoveItem(index, e) {
                if (e) e.preventDefault();
                var newItems = items.filter(function (_, i) {
                    return i !== index;
                });
                props.setAttributes({ items: newItems });
            }

            function updateItem(index, property, value) {
                var newItems = [...items];
                newItems[index] = {
                    ...newItems[index],
                    [property]: value
                };
                props.setAttributes({ items: newItems });
            }

            function onSelectImage(index, media) {
                var newItems = [...items];
                newItems[index] = {
                    ...newItems[index],
                    imageUrl: media.url,
                    imageId: media.id,
                    imageAlt: media.alt || ''
                };
                props.setAttributes({ items: newItems });
            }

            function removeImage(index) {
                var newItems = [...items];
                newItems[index] = {
                    ...newItems[index],
                    imageUrl: '',
                    imageId: null,
                    imageAlt: ''
                };
                props.setAttributes({ items: newItems });
            }

            return el('div', { className: 'wp-block-ello-accordion-block' },
                items.map(function (item, index) {
                    return el('details', {
                        key: 'accordion-' + index,
                        className: 'accordion-item'
                    },
                        el('summary', { className: 'accordion-header' },
                            el(RichText, {
                                key: 'title-' + index,
                                identifier: 'title-' + index,
                                tagName: 'span',
                                value: item.title,
                                onChange: function (value) {
                                    updateItem(index, 'title', value);
                                },
                                placeholder: 'Enter title...',
                                keepPlaceholderOnFocus: true
                            }),
                            items.length > 1 && el('button', {
                                className: 'remove-item',
                                onClick: function (e) {
                                    onRemoveItem(index, e);
                                }
                            }, '×')
                        ),
                        el('div', { className: 'accordion-content' },
                            el('div', { className: 'image-controls' },
                                el(MediaUpload, {
                                    onSelect: function (media) {
                                        onSelectImage(index, media);
                                    },
                                    allowedTypes: ['image'],
                                    value: item.imageId,
                                    render: function (obj) {
                                        return el('div', null,
                                            item.imageUrl && el('div', { className: 'image-preview' },
                                                el('img', {
                                                    src: item.imageUrl,
                                                    alt: item.imageAlt
                                                }),
                                                el(Button, {
                                                    isSecondary: true,
                                                    onClick: function () {
                                                        removeImage(index);
                                                    }
                                                }, 'Remove Image')
                                            ),
                                            !item.imageUrl && el(Button, {
                                                isSecondary: true,
                                                onClick: obj.open
                                            }, 'Upload Image')
                                        );
                                    }
                                })
                            ),
                            el(RichText, {
                                key: 'content-' + index,
                                identifier: 'content-' + index,
                                tagName: 'div',
                                value: item.content,
                                onChange: function (value) {
                                    updateItem(index, 'content', value);
                                },
                                placeholder: 'Enter content...',
                                keepPlaceholderOnFocus: true,
                                multiline: 'p'
                            })
                        )
                    );
                }),
                el('button', {
                    className: 'add-item-button',
                    onClick: onAddItem
                }, 'Add Accordion Item')
            );
        },

        save: function () {
            return null;
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
));
