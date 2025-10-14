/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * InnerBlocks template with pre-configured heading and content
 * Edit this template to customize the default structure
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Section Heading',
    fontFamily: 'montserrat',
    fontSize: '3xl',
    textAlign: 'center',
    textColor: 'main',
    style: {
      typography: {
        fontWeight: '700',
        lineHeight: '1.2'
      },
      spacing: {
        margin: { bottom: '2rem' }
      }
    }
  }],
  ['core/paragraph', {
    content: 'Section description goes here. Replace this with your own content.',
    fontFamily: 'open-sans',
    fontSize: 'base',
    textAlign: 'center',
    textColor: 'secondary',
    style: {
      typography: {
        lineHeight: '1.7'
      }
    }
  }]
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
