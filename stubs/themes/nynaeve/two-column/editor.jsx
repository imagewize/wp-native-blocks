/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Two-column layout template with headings and content areas
 * Edit this template to customize the default structure
 */
const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Two Column Section',
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
        margin: { bottom: '3rem' }
      }
    }
  }],
  ['core/columns', {
    style: {
      spacing: {
        blockGap: '3.75rem'
      }
    }
  }, [
    ['core/column', {}, [
      ['core/group', {
        style: {
          spacing: {
            padding: { top: '2rem', right: '2rem', bottom: '2rem', left: '2rem' }
          },
          border: {
            radius: '0.5rem'
          }
        },
        backgroundColor: 'tertiary'
      }, [
        ['core/heading', {
          level: 3,
          content: 'Column 1 Heading',
          fontFamily: 'montserrat',
          fontSize: '2xl',
          textColor: 'main',
          style: {
            typography: {
              fontWeight: '600',
              lineHeight: '1.3'
            },
            spacing: {
              margin: { bottom: '1rem' }
            }
          }
        }],
        ['core/paragraph', {
          content: 'Add your content here. This is a card-style column with padding and background.',
          fontFamily: 'open-sans',
          fontSize: 'base',
          textColor: 'secondary',
          style: {
            typography: {
              lineHeight: '1.7'
            }
          }
        }]
      ]]
    ]],
    ['core/column', {}, [
      ['core/group', {
        style: {
          spacing: {
            padding: { top: '2rem', right: '2rem', bottom: '2rem', left: '2rem' }
          },
          border: {
            radius: '0.5rem'
          }
        },
        backgroundColor: 'tertiary'
      }, [
        ['core/heading', {
          level: 3,
          content: 'Column 2 Heading',
          fontFamily: 'montserrat',
          fontSize: '2xl',
          textColor: 'main',
          style: {
            typography: {
              fontWeight: '600',
              lineHeight: '1.3'
            },
            spacing: {
              margin: { bottom: '1rem' }
            }
          }
        }],
        ['core/paragraph', {
          content: 'Add your content here. This is a card-style column with padding and background.',
          fontFamily: 'open-sans',
          fontSize: 'base',
          textColor: 'secondary',
          style: {
            typography: {
              lineHeight: '1.7'
            }
          }
        }]
      ]]
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
