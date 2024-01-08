import { useState, useEffect } from "@wordpress/element";
import { setDefaults } from "./config";
import View from "./components/View";
import DevDebug from "./components/DevDebug";
import Inspector from "./components/Inspector";
import SearchModal from "./components/SearchModal";
import "./editor.scss";

export default function Edit({ attributes, setAttributes }) {
  const [isOpenModal, setOpenModal] = useState(false);
  const debugState = useState({ block: false, classes: false });
  const openModal = () => setOpenModal(true);
  const closeModal = () => setOpenModal(false);

  useEffect(() => {
    setDefaults(attributes, setAttributes);
  }, []);

  return (
    <>
      <Inspector
        attributes={attributes}
        setAttributes={setAttributes}
        openModal={openModal}
        isOpenModal={isOpenModal}
        debugState={debugState}
      />
      {isOpenModal && (
        <SearchModal
          attributes={attributes}
          setAttributes={setAttributes}
          close={closeModal}
          isOpen={isOpenModal}
        />
      )}
      <View attributes={attributes} />
      <DevDebug debugState={debugState} attributes={attributes} />
    </>
  );
}
