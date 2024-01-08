import { useState, useEffect } from "@wordpress/element";
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";
import { Placeholder } from "@wordpress/components";
import Inspector from "./inspector";
import "./editor.scss";

const defaults = {
  layout: "billboard",
};

const layoutNames = {
  billboard: "Billboard",
  header: "Header",
  arroba: "Arroba",
  halfpage: "Halfpage",
};

const layoutSizes = {
  billboard: { height: "250px" },
  header: { height: "150px" },
  arroba: { width: "300px", height: "250px" },
  halfpage: { width: "300px", height: "600px" },
};

export default function Edit({ attributes, setAttributes }) {
  const debugState = useState({ block: false, classes: false });
  const [debug, setDebug] = debugState;
  function setDefaults(defaults) {
    const items = Object.entries(defaults);
    const save = {};
    for (const [key, value] of items) {
      if (attributes[key] !== undefined) continue;
      save[key] = value;
    }
    setAttributes({ ...save });
  }

  useEffect(() => {
    setDefaults(defaults);
  }, []);

  const styles = [`ads--${attributes.format}`];
  const className = styles.join(" ");
  const blockProps = useBlockProps({ className });
  const innerBlocksProps = useInnerBlocksProps(blockProps, attributes);

  return (
    <>
      <Inspector
        attributes={attributes}
        setAttributes={setAttributes}
        debugState={debugState}
      />
      <aside {...innerBlocksProps}>
        <Placeholder
          icon="money"
          label={layoutNames[attributes.layout]}
          style={{
            alignItems: "center",
            textTransform: "uppercase",
            ...layoutSizes[attributes.layout],
          }}
          className="wp-block"
        />
      </aside>
      {debug.block && (
        <div>
          <code
            style={{ display: "block" }}
            dangerouslySetInnerHTML={{
              __html: JSON.stringify(attributes, null, 2),
            }}
          />
        </div>
      )}
      {debug.classes && (
        <div>
          <code
            style={{ display: "block" }}
            dangerouslySetInnerHTML={{
              __html: JSON.stringify(styles, null, 2),
            }}
          />
        </div>
      )}
    </>
  );
}
