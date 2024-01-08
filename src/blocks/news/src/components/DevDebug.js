import { getStyles } from "../config";

export default function DevDebug({
  attributes,
  debugState: [debug, setDebug],
}) {
  const styles = getStyles(attributes);
  return (
    <>
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
