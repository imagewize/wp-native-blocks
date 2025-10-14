/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Two-column layout template with minimal configuration
 * Customize the TEMPLATE array below to match your needs
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Two Column Section',
  }],
  ['core/columns', {}, [
    ['core/column', {}, [
      ['core/heading', {
        level: 3,
        content: 'Column 1 Heading',
      }],
      ['core/paragraph', {
        content: 'Add your content for the first column here.',
      }]
    ]],
    ['core/column', {}, [
      ['core/heading', {
        level: 3,
        content: 'Column 2 Heading',
      }],
      ['core/paragraph', {
        content: 'Add your content for the second column here.',
      }]
    ]]
  ]]
];

/**
 * Edit function that renders in the admin
 */
export default function Edit() {
  const blockProps = useBlockProps({
    className: '{{BLOCK_CLASS_NAME}}'
  });

  return (
    <div { ...blockProps }>
      <InnerBlocks
        template={TEMPLATE}
        templateLock={false}
      />
    </div>
  );
}
