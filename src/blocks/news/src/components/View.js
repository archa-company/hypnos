import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";
import { getStyles } from "../config";

export default function View({ attributes }) {
  const className = getStyles(attributes);
  const blockProps = useBlockProps({ className });
  const innerBlocksProps = useInnerBlocksProps(blockProps, attributes);

  return (
    <article {...innerBlocksProps}>
      <div class="news__wrapper">
        {attributes.hasImage && attributes.image && (
          <>
            {attributes.layout === "highlight" && (
              <div
                class="news__image"
                style={{
                  backgroundImage: `url(${attributes.image})`,
                  backgroundPositionX: `${attributes.imagePosX * 100}%`,
                  backgroundPositionY: `${attributes.imagePosY * 100}%`,
                }}
              />
            )}
            {attributes.layout !== "highlight" && (
              <div class="news__image">
                <img
                  src={attributes.image}
                  alt={attributes.title}
                  style={{
                    objectPosition: `${attributes.imagePosX * 100}% ${
                      attributes.imagePosY * 100
                    }%`,
                  }}
                />
              </div>
            )}
          </>
        )}
        <div class="news__content">
          <div class="news__hat">
            <span>{attributes.hat}</span>
          </div>
          <h2 class="news__title">
            <span>{attributes.title}</span>
          </h2>
          {attributes.sponsored && attributes?.sponsorName && (
            <div class="news__sponsor">por {attributes.sponsorName}</div>
          )}
        </div>
      </div>
      {attributes?.relateds && (
        <ul class="news__relateds">
          {attributes.relateds.map((related) => (
            <li>
              <span>{related.title}</span>
            </li>
          ))}
        </ul>
      )}
    </article>
  );
}
