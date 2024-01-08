export const colors = [
  { name: "preto", color: "#2b2b2b" },
  { name: "athletico", color: "#f00201" },
  { name: "azul", color: "#0e5070" },
  { name: "coritiba", color: "#14483d" },
  { name: "amarelo", color: "#ffd937" },
  { name: "bege", color: "#e0daB8" },
  { name: "cinza", color: "#eee" },
  { name: "branco", color: "#fff" },
];

export const defaults = {
  limit: 3,
  columns: 3,
  title: "Blogs",
  titleTextColor: "#2b2b2b",
  titleBorderColor: "#2b2b2b",
  backgroundColor: "#ffd937",
  align: "wide",
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
