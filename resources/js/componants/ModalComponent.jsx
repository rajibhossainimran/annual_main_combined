import React from "react";
import { Modal } from "react-bootstrap";

const ModalComponent = (props) => {
  const { show, handleClose, size, modalTitle } = props;
  return (
    <>
      <Modal onClose={handleClose} size={size} show={show} onHide={handleClose}>
        <Modal.Header closeButton>
          <Modal.Title>{modalTitle}</Modal.Title>
          <p className="btn-modal-close cursor-pointer" onClick={() => handleClose()}>
            <i className="fa fa-times text-danger" onClick={() => handleClose()}></i>
          </p>
        </Modal.Header>
        <Modal.Body>{props.children}</Modal.Body>
      </Modal>
    </>
  );
};

export default ModalComponent;