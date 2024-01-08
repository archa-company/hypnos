export const defaults = {
  layout: "vertical",
  format: "default",
  size: "medium",
  titleBgGray: false,
  hasImage: true,
  inverted: false,
  boxed: false,
  sponsored: false,
};

export const options = {
  layout: [
    { label: "Vertical", value: "vertical" },
    { label: "Horizontal", value: "horizontal" },
    { label: "Destaque", value: "highlight" },
  ],
  format: [
    { label: "Padrão", value: "default" },
    { label: "Story", value: "story" },
    { label: "Vídeo", value: "video" },
    { label: "Galeria", value: "gallery" },
  ],
  size: [
    { label: "XG", value: "giga" },
    { label: "GG", value: "mega" },
    { label: "G", value: "large" },
    { label: "M", value: "medium" },
    { label: "P", value: "small" },
  ],
  sponsorTypes: [
    { label: "Publicitário", value: "Especial Publicitário" },
    { label: "Patrocinado", value: "Especial Patrocinado" },
  ],
};

export function setDefaults(attributes, setAttributes) {
  const items = Object.entries(defaults);
  const save = {};
  for (const [key, value] of items) {
    if (attributes[key] !== undefined) continue;
    save[key] = value;
  }
  setAttributes({ ...save });
}

export function getStyles(attributes) {
  const styles = [];
  if (attributes?.layout) styles.push(`news--${attributes.layout}`);
  if (attributes?.format) styles.push(`news--${attributes.format}`);
  if (attributes?.size) styles.push(`news--${attributes.size}`);
  if (attributes?.hasImage && attributes.image) styles.push("news--has-image");
  if (!attributes?.hasImage || !attributes.image) styles.push("news--no-image");
  if (attributes?.titleBgGray) styles.push("news--title-gray");
  if (attributes?.inverted) styles.push("news--inverted");
  if (attributes?.boxed) styles.push("news--boxed");
  if (attributes?.sponsored) styles.push("news--sponsored");
  return styles.join(" ");
}
