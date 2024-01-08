import { useEffect } from "@wordpress/element";
import { InnerBlocks, useBlockProps } from "@wordpress/block-editor";
import { setDefaults } from "./config";
import Inspector from "./components/Inspector";
import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
  useEffect(() => {
    setDefaults(attributes, setAttributes);
  }, []);

  const blockProps = useBlockProps();
  const template = [];
  for (let i = 1; i <= attributes.limit; i++) {
    template.push([
      "morpheus/news",
      {
        className: "wp-block-morpheus-blogs__item",
        layout: "vertical",
        size: "medium",
        hat: "Blogs",
        title: "Selecione uma matéria",
        link: "#",
        image: "",
        boxed: true,
      },
    ]);
  }
  const innerBlocks = {
    allowedBlocks: ["morpheus/news"],
    renderAppender: false,
    template,
  };
  console.log("Morpheus Blogs - innerBlocks:", innerBlocks);
  return (
    <>
      <Inspector attributes={attributes} setAttributes={setAttributes} />
      <div
        {...blockProps}
        style={{ backgroundColor: attributes.backgroundColor }}
      >
        <style>
          {`
          #${blockProps.id} .wp-block-morpheus-blogs__items,
          #${
            blockProps.id
          } .block-editor-inner-blocks > .block-editor-block-list__layout {
            grid-template-columns: repeat(${attributes.columns || 2}, 1fr);
          }
          `}
        </style>
        <div className="wp-block wp-block-morpheus-blogs__container">
          <h2 className="wp-block-morpheus-blogs__title">
            <span
              style={{
                borderColor: attributes.titleBorderColor,
                color: attributes.titleTextColor,
              }}
            >
              {attributes.title}
            </span>
          </h2>
          <InnerBlocks {...innerBlocks} />
        </div>
      </div>
    </>
  );
}
