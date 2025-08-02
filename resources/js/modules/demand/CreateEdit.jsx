import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import { PatternFormat } from 'react-number-format';
import moment from 'moment';
import DatePicker from "react-datepicker";
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import "react-datepicker/dist/react-datepicker.css";

export default function CreateEdit() {

  const [type, setType] = useState()
  const [fy, setFy] = useState('')
  const [DemandCategory, setDemandCategory] = useState('')
  const [demandDate, setDemandDate] = useState()
  const [demadType, setDemandType] = useState()
  const [signalNo, setSignalNo] = useState()
  const [DemandNo, setDemandNo] = useState()
  const [isCustomDemandNo, setIsCustomDemandNo] = useState(false)
  const [pradhikarNo, setPradhikarNo] = useState()
  const [description, setDescription] = useState('1.   This is to info that the fol item is urgently reqr for Central Med Store of this hosp:')
  const [description1, setDescription1] = useState('')
  const [indentNo, setIndentNo] = useState()
  const [isSignal, setIsSignal] = useState()
  const [demandPVMS, setDemandPVMS] = useState([])
  const [demandTypes, setDemandTypes] = useState([])
  const [PatientUnits, setPatientUnits] = useState([])
  const [financialYears, setFinancialYears] = useState([])
  const [isFormSubmited, setIsFormSubmited] = useState(false)
  const [isFormSavingasTemplate, setIsFormSavingasTemplate] = useState(false)
  const [demandId, setDemandId] = useState()
  const [diseaseList, setDiseaseList] = useState([])
  const [PatientList, setPatientList] = useState([])
  const [currentDiseaseIndex, setCurrentDiseaseIndex] = useState()
  const [currentPatientIndex, setCurrentPatientIndex] = useState()
  const [IsShowModal, setIsShowModal] = useState(false)
  const [IsShowSaveTemplateModal, setIsShowSaveTemplateModal] = useState(false)
  const [IsPublished, setIsPublished] = useState(0)
  const [DemandItemType, setDemandItemType] = useState()
  const [DemandItemTypeMultiple, setDemandItemTypeMultiple] = useState([])
  const [ItemTypeList, setItemTypeList] = useState()
  const [PaitientName, setPaitientName] = useState()
  const [PaitientIdentificationType, setPaitientIdentificationType] = useState()
  const [PaitientIdentificationNumber, setPaitientIdentificationNumber] = useState()
  const [PaitientIdentificationRelation, setPaitientIdentificationRelation] = useState()
  const [PaitientIdentificationUnit, setPaitientIdentificationUnit] = useState()
  const [IsPatientError, setIsPatientError] = useState(false)
  const [DemandNoError, setDemandNoError] = useState('')
  const [userApproval, setUserApproval] = useState()
  const [userInfo, setUserInfo] = useState('')
  const [isDentalType, setIsDentalType] = useState(false)
  const [selectedFile, setSelectedFile] = useState()
  const [selectedFiles, setSelectedFiles] = useState([])
  const [documentFile, setDocumentFile] = useState()
  const [documentFiles, setDocumentFiles] = useState()
  const [TemplateName, setTemplateName] = useState('')

  // repair states
  const [issueDate, setIssueDate] = useState()
  const [installationDate, setInstallationDate] = useState()
  const [warranty, setWarranty] = useState()
  const [supplier, setSupplier] = useState()
  const [authorizedMachine, setAuthorizedMachine] = useState()
  const [existingMachine, setExistingMachine] = useState()
  const [runningMachine, setRunningMachine] = useState()
  const [disabledMachine, setDisabledMachine] = useState()
  const [repairPVMS, setRepairPVMS] = useState([])
  const [DemandNoChecking, setDemandNoChecking] = useState(false)

  useEffect(() => {
    if (PaitientIdentificationNumber && PaitientIdentificationType && PaitientIdentificationRelation) {
      axios.get(`${window.app_url}/patient-check-indetification_no?relation=${PaitientIdentificationRelation}&identification_no=${PaitientIdentificationType}-${PaitientIdentificationNumber}`).then((res) => {
        if (res.data) {
          setIsPatientError(true);
        } else {
          setIsPatientError(false)
        }
      })
    }
  }, [PaitientIdentificationType, PaitientIdentificationNumber, PaitientIdentificationRelation, 500])

  useEffect(() => {
    if (DemandNo) {
      if (!DemandNo.includes('_')) {
        setDemandNoChecking(true);
        axios.get(window.app_url + '/uniq_demand_no/' + DemandNo).then((res) => {
          setDemandNoError('');
          setDemandNoChecking(false);
        })
      }
    }
  }, [DemandNo]);


  const DemandSaveTemplateSubmit = (e) => {
    e.preventDefault();
    const request_data = {
      template_name: TemplateName,
      demand_item_type_id: DemandItemType.id,
      'demadType': demadType ? demadType : '',
      description,
      description1,
      demandPVMS,
      repairPVMS,
      isDentalType
    }

    setIsFormSavingasTemplate(true)

    const data = new FormData();
    data.append('data', JSON.stringify(request_data))
    data.append('document_file', selectedFiles)
    for (let i = 0; i < selectedFiles.length; i++) {
      data.append('document_files[]', selectedFiles[i]);
    }
    axios.post(window.app_url + '/demand-template', data).then((res) => {
      setIsFormSavingasTemplate(false);
      setIsShowSaveTemplateModal(false);
    }).catch((err) => {
      setIsFormSavingasTemplate(false);
      setIsShowSaveTemplateModal(false);
      if (err.response?.data?.message) {
        window.scroll(0, 0);
        Swal.fire({
          icon: 'error',
          text: err.response?.data?.message,
        })
      }

    })

  }

  const AddPatientSubmit = (e) => {
    e.preventDefault();
    let request_data = {
      'name': PaitientName,
      'identification_no': `${PaitientIdentificationType}-${PaitientIdentificationNumber}`,
      'relation': PaitientIdentificationRelation,
      'unit_id': PaitientIdentificationUnit,
    }
    axios.post(window.app_url + '/patient', request_data).then((res) => {
      setIsShowModal(false);
    }).catch(() => {

    })
  }

  const loadOptions = (inputValue, callback) => {
    axios.get(window.app_url + '/settings/pvms/search?keyword=' + inputValue).then((res) => {
      const data = res.data;
      let option = [];
      for (const iterator of data) {
        let controlTypeLabel = 'N/A';
        if (iterator.control_types_id === 1) {
          controlTypeLabel = 'Control';
        } else if (iterator.control_types_id === 2) {
          controlTypeLabel = 'NonControl';
        }

        option.push({ value: iterator.id, label: controlTypeLabel + ' - ' + iterator.pvms_id + ' - ' + iterator.nomenclature + ' - ' + (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data: iterator })
      }
      callback(option);
    })
  };
  const loadDemandTemplateOptions = (inputValue, callback) => {
    axios.get(window.app_url + '/demand-template?keyword=' + inputValue).then((res) => {
      const data = res.data;

      let option = [];
      for (const iterator of data) {
        option.push({ value: iterator.id, label: iterator.template_name, data: iterator })
      }

      callback(option);
    })
  };


const pvmsChange = (value) => {
  const selectedControlType = value.data.control_types_id;

  // ✅ Get existing control types from demandPVMS
  const existingControlTypes = demandPVMS.map(i => i.control_types_id).filter(Boolean);

  // ✅ Show alert if trying to mix control_type_id = 1 and 2
  if (
    (existingControlTypes.includes(1) && selectedControlType == 2) ||
    (existingControlTypes.includes(2) && selectedControlType == 1)
  ) {
    Swal.fire({
        icon: 'warning',
        title: 'Invalid Selection',
        text: `You cannot select ${selectedControlType === 1 ? 'Control' : 'Non-Control'} type because ${existingControlTypes.includes(1) ? 'Control' : 'Non-Control'} type already exists.`,
        });
    return;
  }

  const pvms_exists = demandPVMS.find(i => i.id == value.value);

  if (pvms_exists && demadType != 1) {
    setDemandPVMS((prev) => {
      let copy = [...prev];
      let findPvmsIndex = copy.findIndex(item => item.id == value.value);
      if (findPvmsIndex > -1) {
        copy[findPvmsIndex].qty = parseInt(copy[findPvmsIndex].qty) + 1;
      }
      return copy;
    });
  } else {
    const { id, nomenclature, unit_name, pvms_id, item_typename, authorized_equipment } = value.data;

    // ✅ Add item type if not already added
    setDemandItemTypeMultiple((prev) => {
      if (!prev.find(i => i.id == item_typename.id)) {
        prev.push(item_typename);
      }
      return prev;
    });

    const new_demand_pvms = {
      id,
      pvms_id,
      nomenclature,
      au: unit_name?.name,
      item_type: item_typename.name,
      item_id: item_typename.id,
      qty: null,
      prev_purchase: null,
      present_stock: null,
      proposed_reqr: null,
      patient_name: '',
      patient_id: '',
      authorized_machine: authorized_equipment ? authorized_equipment.authorized_number : '',
      existing_machine: authorized_equipment ? authorized_equipment.available_number : '',
      running_machine: '',
      disabled_machine: '',
      ward: '',
      disease: '',
      remarks: '',
      demand_pvms_id: null,
      control_types_id: selectedControlType // ✅ Save control_type_id for future checks
    };

    if (demandId) {
      if (DemandItemType && DemandItemType.id != item_typename.id) {
        Swal.fire({
          icon: 'error',
          text: `You're choosing ${item_typename.name} type of PVMS item. But existing demand item type is ${DemandItemType.name}.`
        });
        return;
      }
    }

    if (demandPVMS.length == 0) {
      setDemandItemType(item_typename);
    }

    setDemandPVMS((prev) => {
      let copy = [...prev];
      copy.push(new_demand_pvms);
      return copy;
    });
  }
}



//   const pvmsChange = (value) => {
//     console.log(value.data.control_types_id);
//     const pvms_exists = demandPVMS.find(i => i.id == value.value)

//     if (pvms_exists && demadType != 1) {
//       setDemandPVMS((prev) => {
//         let copy = [...prev]
//         let findPvmsIndex = copy.findIndex(item => item.id == value.value);
//         if (findPvmsIndex > -1) {
//           copy[findPvmsIndex].qty = parseInt(copy[findPvmsIndex].qty) + 1;
//         }
//         return copy
//       })

//     } else {
//       const { id, nomenclature, unit_name, pvms_id, item_typename, authorized_equipment } = value.data
//       setDemandItemTypeMultiple((prev) => {
//         if (!prev.find(i => i.id == item_typename.id)) {
//           prev.push(item_typename)
//         }
//         return prev
//       })

//       const new_demand_pvms = {
//         id,
//         pvms_id,
//         nomenclature,
//         au: unit_name?.name,
//         item_type: item_typename.name,
//         item_id: item_typename.id,
//         qty: null,
//         prev_purchase: null,
//         present_stock: null,
//         proposed_reqr: null,
//         patient_name: '',
//         patient_id: '',
//         authorized_machine: authorized_equipment ? authorized_equipment.authorized_number : '',
//         existing_machine: authorized_equipment ? authorized_equipment.available_number : '',
//         running_machine: '',
//         disabled_machine: '',
//         ward: '',
//         disease: '',
//         remarks: '',
//         demand_pvms_id: null
//       }

//       if (demandId) {
//         if (DemandItemType && DemandItemType.id != item_typename.id) {
//           Swal.fire({
//             icon: 'error',
//             // title: 'Oops...',
//             text: `You're choosing ${item_typename.name} type of PVMS item.But existing demand item type is ${DemandItemType.name}.`,
//             // footer: '<a href="">Why do I have this issue?</a>'
//           })

//           return;
//         }
//       }

//       if (demandPVMS.length == 0) {
//         setDemandItemType(item_typename)
//       }

//       setDemandPVMS((prev) => {
//         let copy = [...prev]
//         copy.push(new_demand_pvms)
//         console.log(copy);

//         return copy
//       })
//     }
//   }

  const repairChange = (value) => {
    const pvms_exists = demandPVMS.find(i => i.id == value.value)

    if (pvms_exists) {
      setRepairPVMS((prev) => {
        let copy = [...prev]
        let findPvmsIndex = copy.findIndex(item => item.id == value.value);
        if (findPvmsIndex > -1) {
          copy[findPvmsIndex].qty = parseInt(copy[findPvmsIndex].qty) + 1;
        }
        return copy
      })

    } else {
      const { id, nomenclature, unit_name, pvms_id, item_typename, authorized_equipment } = value.data
      console.log(authorized_equipment);
      const new_demand_pvms = {
        id,
        pvms_id,
        nomenclature,
        issue_date: '',
        installation_date: '',
        warranty_date: '',
        authorized_machine: authorized_equipment ? authorized_equipment.authorized_number : 'N/A',
        existing_machine: authorized_equipment ? authorized_equipment.available_number : 'N/A',
        running_machine: '',
        disabled_machine: '',
        supplier: '',
        remarks: '',
        demand_pvms_id: null
      }

      if (DemandItemType && DemandItemType.id != item_typename.id) {
        Swal.fire({
          icon: 'error',
          // title: 'Oops...',
          text: `You're choosing ${item_typename.name} type of PVMS item.But existing demand item type is ${DemandItemType.name}.`,
          // footer: '<a href="">Why do I have this issue?</a>'
        })

        return;
      }

      if (demandPVMS.length == 0) {
        setDemandItemType(item_typename)
      }

      setRepairPVMS((prev) => {
        let copy = [...prev]
        copy.push(new_demand_pvms)
        return copy
      })
    }
  }

  const handleChangePvms = (value) => {
    if (demadType == 4) {
      repairChange(value)
    } else {
      pvmsChange(value)
    }
  }

  const handleSelectDemadTemplate = (item, select) => {
    debugger
    if (select.action === "clear") {

    } else if (select.action === "select-option") {
      Swal.fire({
        icon: 'warning',
        text: `Do you want to Load Template ${item.label}?`,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true
      }).then((r) => {
        if (r.isConfirmed) {
          let data = item.data;
          setDemandType(data.demand_type_id)
          setDemandItemType(data?.demandItemType)
          setDescription(data.description)
          if (data.description1) {
            setDescription1(data.description1)
          }
          setIsDentalType(data.is_dental_type)
          setDocumentFile(data.document_file)

          if (data.demand_type_id == 4) {
            populateDemandTemplateRepairPvms(data)
          } else {
            populateDemandTemplatePvms(data)
          }
        }
      })

    }

  }

  const handleDeletePVMS = (index) => {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-success ml-2',
        cancelButton: 'btn btn-danger mr-2'
      },
      buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
      title: 'Are you sure?',
      text: "You want to delete this row!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, cancel!',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {

        if (demandId && demandPVMS[index] && demandPVMS[index].demand_pvms_id) {
          axios.post(window.app_url + `/remove-demand-pvms/${demandPVMS[index].demand_pvms_id}`).then((res) => {

          }).catch(() => {

          })
        }
        setDemandPVMS((prev) => {
          let copy = [...prev]

          return copy.filter((val, key) => {
            if (index != key) {
              return true;
            }
          })
        })

        swalWithBootstrapButtons.fire(
          'Deleted!',
          'Row has been deleted.',
          'success'
        )
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        // swalWithBootstrapButtons.fire(
        //   'Cancelled',
        //   'Your imaginary file is safe :)',
        //   'error'
        // )
      }
    })
  }

  const handleDemandPVMSValueChange = (e, index) => {

    if (e.name == 'disease') {
      axios.get(window.app_url + '/disease?keyword=' + e.value).then((res) => {
        let items = []
        for (const iterator of res.data) {
          items.push({ value: iterator.id, name: iterator.diseases_name })
        }
        setDiseaseList(items)
      })
    }
    if (e.name == 'patient_name') {
      axios.get(window.app_url + '/patient-search?keyword=' + e.value).then((res) => {
        let items = []
        for (const iterator of res.data) {
          let patient_desc = iterator.relation == 'Self' ? `${iterator.identification_no} ${iterator.name} Unit: ${iterator.unit_from?.name}` : `${iterator.relation} ${iterator.identification_no} ${iterator.name} Unit: ${iterator.unit_from.name}`;

          items.push({ id: iterator.id, name: patient_desc })
        }
        setPatientList(items)
      })
    }

    setDemandPVMS((prev) => {
      let copy = [...prev]

      copy[index] = { ...copy[index], [e.name]: e.value };

      return copy
    })
  }

  const handleDemandRepairDateValueChange = (value, index, field) => {
    setRepairPVMS((prev) => {
      let copy = [...prev]

      copy[index] = { ...copy[index], [field]: value };

      return copy
    })
  }

  const handleDemandRepairValueChange = (e, index) => {
    setRepairPVMS((prev) => {
      let copy = [...prev]

      copy[index] = { ...copy[index], [e.name]: e.value };

      return copy
    })
  }

  const handleSelectDisease = (value, index) => {
    setDemandPVMS((prev) => {
      let copy = [...prev]

      copy[index] = { ...copy[index], disease: value.name };

      return copy
    })
  }
  const handleSelectPatient = (value, index) => {
    setDemandPVMS((prev) => {
      let copy = [...prev]

      copy[index] = { ...copy[index], patient_name: value.name, patient_id: value.id };

      return copy
    })
  }

  const handleChangeDemandType = (e) => {
    setDemandType(e.target.value)

    if (e.target.value == 4) {
      setDemandItemType(ItemTypeList[0])
    }

    const selected_demad = demandTypes.find(i => i.id == e.target.value);

    if (selected_demad.name == 'Signal') {
      setIsSignal(true)
    } else {
      setIsSignal(false)
    }
  }

  const handleDemandPVMSSubmit = (e) => {
    e.preventDefault();
    if (DemandNoError) {
      window.scroll(0, 0);
      Swal.fire({
        icon: 'error',
        text: DemandNoError,
      })
      return;
    }
    if ((!isCustomDemandNo && !demandId) && (DemandNo.includes('_') || DemandNo.length < 34)) {
      setDemandNoError('Please Fill Up Demand Number')
      window.scroll(0, 0);
      return;
    } else {
      setDemandNoError('')
    }

    const request_data = {
      type,
      demand_item_type_id: DemandItemType.id,
      fy,
      demandDate,
      demadType,
      pradhikarNo,
      indentNo,
      signalNo,
      description,
      description1,
      demandPVMS,
      repairPVMS,
      is_published: IsPublished,
      DemandNo,
      isDentalType,
      'demand_category': DemandCategory
    }

    setIsFormSubmited(true)

    if (demandId) {
      axios.put(window.app_url + '/demand/' + demandId, request_data).then((res) => {

        // window.location.href = window.demand_index_url

        let pvms = [];
        for (const iterator of res.data.demand_p_v_m_s) {
          const p_v_m_s = iterator.p_v_m_s

          pvms.push({
            id: p_v_m_s.id,
            pvms_id: p_v_m_s.pvms_id,
            nomenclature: p_v_m_s.nomenclature,
            au: p_v_m_s.unit_name?.name,
            item_type: p_v_m_s.item_typename.name,
            present_stock: 0,
            // unit_pre_stock:iterator.unit_pre_stock,
            // avg_expense:iterator.avg_expense,
            patient_name: iterator.patient_name,
            patient_id: iterator.patient_id,
            disease: iterator.disease,
            qty: iterator.qty,
            remarks: iterator.remarks,
            demand_pvms_id: iterator.id
          })
        }
        setDemandPVMS(pvms)
        setIsFormSubmited(false)
        window.location.href = window.demand_index_url
      }).catch((err) => {
        setIsFormSubmited(false)
      })

      if (selectedFiles.length > 0) {
        const data = new FormData();
        data.append('document_file', selectedFile)
        for (let i = 0; i < selectedFiles.length; i++) {
          data.append('document_files[]', selectedFiles[i]);
        }
        axios.post(window.app_url + '/demand-update-document/' + demandId, data).then((res) => {

        })

      }
    } else {
      const data = new FormData();
      data.append('data', JSON.stringify(request_data))
      data.append('document_file', selectedFile)
      for (let i = 0; i < selectedFiles.length; i++) {
        data.append('document_files[]', selectedFiles[i]);
      }
      axios.post(window.app_url + '/demand', data).then((res) => {
        setIsFormSubmited(false)
        window.location.href = window.demand_index_url
      }).catch((err) => {
        setIsFormSubmited(false)
        if (err.response?.data?.message) {
          window.scroll(0, 0);
          Swal.fire({
            icon: 'error',
            text: err.response?.data?.message,
          })
        }

      })
    }

  }

  const isDigit = (value) => {
    var regex = new RegExp("^[a-zA-Z .]*$");
    if (regex.test(value)) {
      return false
    } else {
      return true
    }
  }

  const handleChangeFile = (files) => {

    for (let index = 0; index < files.length; index++) {
      const file = files[index];
      if ((file.size / 1024 / 1024) > 10) {
        Swal.fire({
          text: "File size must me under 10 mb",
          icon: 'error',
        })

        return;
      }
    }
    setSelectedFiles(files);
  }

  const renderFileList = () => (
    <>
      {selectedFiles && selectedFiles.length > 0 && <ol>
        {[...selectedFiles].map((f, i) => (
          <li key={i}>{f.name} - {f.type}</li>
        ))}
      </ol>}
    </>
  )

  const populateDemandPvms = (data) => {
    let pvms = []
    for (const iterator of data.demand_p_v_m_s) {
      const p_v_m_s = iterator.p_v_m_s

      pvms.push({
        id: p_v_m_s.id,
        pvms_id: p_v_m_s.pvms_id,
        nomenclature: p_v_m_s.nomenclature,
        au: p_v_m_s.unit_name?.name,
        item_type: p_v_m_s.item_typename.name,
        patient_name: iterator.patient_name,
        patient_id: iterator.patient_id,
        disease: iterator.disease,
        qty: iterator.qty,
        prev_purchase: iterator?.prev_purchase,
        present_stock: iterator.present_stock ? iterator.present_stock : 0,
        proposed_reqr: iterator?.proposed_reqr,
        remarks: iterator.remarks,
        demand_pvms_id: iterator.id,
        authorized_machine: iterator.authorized_machine,
        existing_machine: iterator.existing_machine,
        running_machine: iterator.running_machine,
        disabled_machine: iterator.disabled_machine,
        ward: iterator.ward,
      })
    }

    if (data.demand_item_type) {
      setDemandItemType(data.demand_item_type)
    }

    setDemandPVMS(pvms)
  }
  const populateDemandTemplatePvms = (data) => {
    let pvms = []
    for (const iterator of data.demand_template_p_v_m_s) {
      debugger
      const p_v_m_s = iterator.p_v_m_s

      pvms.push({
        id: p_v_m_s.id,
        pvms_id: p_v_m_s.pvms_id,
        nomenclature: p_v_m_s.nomenclature,
        au: p_v_m_s.unit_name?.name,
        item_type: p_v_m_s.item_typename.name,
        item_id: p_v_m_s.item_typename.id,
        present_stock: 0,
        patient_name: iterator.patient_name,
        patient_id: iterator.patient_id,
        disease: iterator.disease,
        qty: iterator.qty,
        remarks: iterator.remarks,
        demand_pvms_id: iterator.id,
        authorized_machine: iterator.authorized_machine,
        existing_machine: iterator.existing_machine,
        running_machine: iterator.running_machine,
        disabled_machine: iterator.disabled_machine,
        ward: iterator.ward
      })
      setDemandItemTypeMultiple((prev) => {
        if (!prev.find(i => i.id == p_v_m_s.item_typename.id)) {
          prev.push(p_v_m_s.item_typename)
        }
        return prev
      })
    }

    if (data.demand_item_type) {
      setDemandItemType(data.demand_item_type)
    }

    setDemandPVMS(pvms)
  }

  const populateDemandRepairPvms = (data) => {
    let pvms = []
    for (const iterator of data.demand_repair_p_v_m_s) {
      const p_v_m_s = iterator.p_v_m_s

      pvms.push({
        id: p_v_m_s.id,
        pvms_id: p_v_m_s.pvms_id,
        nomenclature: p_v_m_s.nomenclature,
        issue_date: new Date(iterator.issue_date),
        installation_date: new Date(iterator.installation_date),
        warranty_date: new Date(iterator.warranty_date),
        authorized_machine: iterator.authorized_machine,
        existing_machine: iterator.existing_machine,
        running_machine: iterator.running_machine,
        disabled_machine: iterator.disabled_machine,
        ward: iterator.ward,
        supplier: iterator.supplier,
        remarks: iterator.remarks,
        demand_pvms_id: iterator.id,
        item_id: p_v_m_s.item_typename.id,
      })
      setDemandItemTypeMultiple((prev) => {
        if (!prev.find(i => i.id == p_v_m_s.item_typename.id)) {
          prev.push(p_v_m_s.item_typename)
        }
        return prev
      })
    }

    if (data.demand_item_type) {
      setDemandItemType(data.demand_item_type)
    }

    setRepairPVMS(pvms)
  }
  const populateDemandTemplateRepairPvms = (data) => {
    let pvms = []
    for (const iterator of data.demand_template_p_v_m_s) {
      const p_v_m_s = iterator.p_v_m_s

      pvms.push({
        id: p_v_m_s.id,
        pvms_id: p_v_m_s.pvms_id,
        nomenclature: p_v_m_s.nomenclature,
        issue_date: new Date(iterator.issue_date),
        installation_date: new Date(iterator.installation_date),
        warranty_date: new Date(iterator.warranty_date),
        authorized_machine: iterator.authorized_machine,
        existing_machine: iterator.existing_machine,
        running_machine: iterator.running_machine,
        disabled_machine: iterator.disabled_machine,
        ward: iterator.ward,
        supplier: iterator.supplier,
        remarks: iterator.remarks,
        demand_pvms_id: iterator.id
      })
    }

    if (data.demand_item_type) {
      setDemandItemType(data.demand_item_type)
    }

    setRepairPVMS(pvms)
  }

  const handleChangeItemType = (e) => {
    let findItem = ItemTypeList.findIndex(i => i.id == e.target.value)
    if (demandPVMS.length > 0) {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success ml-2',
          cancelButton: 'btn btn-danger mr-2'
        },
        buttonsStyling: false
      })

      swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "Changing item type causes empty PVMS list!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Change it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          setDemandPVMS([])

          if (findItem > -1) {
            setDemandItemType(ItemTypeList[findItem])
          }
        }
      })
    } else {
      if (findItem > -1) {
        setDemandItemType(ItemTypeList[findItem])
      }
    }
  }

  useEffect(() => {
    axios.get(window.app_url + '/demand-types').then((res) => {
      setDemandTypes(res.data)
    })

    axios.get(window.app_url + '/get_all_item_types').then((res) => {
      setItemTypeList(res.data)
    })

    axios.get(window.app_url + '/settings/financial-years/api').then((res) => {
      setFinancialYears(res.data)
    })

    axios.get(window.app_url + '/patient-units').then((res) => {
      setPatientUnits(res.data)
    })


    setDemandId(window.demand_id)

    if (window.demand_id) {
      axios.get(window.app_url + '/demand/api/' + window.demand_id).then((res) => {
        const data = res.data

        setType(data.control_type_id)
        setFy(data.financialYear)
        setDemandDate(data.demand_date)
        setDemandType(data.demand_type_id)
        setIndentNo(data.indent_no)
        setPradhikarNo(data.pradhikar_no)
        setSignalNo(data.signal_no)
        setDemandNo(data.uuid)
        setDescription(data.description)
        if (data.description1) {
          setDescription1(data.description1)
        }
        if (data.demand_category) {
          setDemandCategory(data.demand_category)
        }

        setIsPublished(data.is_published)
        setIsDentalType(data.is_dental_type)
        setDocumentFile(data.document_file)
        if (data.demand_documents) {
          setDocumentFiles(data.demand_documents)
        }

        if (data.demand_type_id == 4) {
          populateDemandRepairPvms(data)
        } else {
          populateDemandPvms(data)
        }

      })
    } else {
      setDemandDate(new Date())
      if (window.suggested_demand_no_prefix) {
        setDemandNo(window.suggested_demand_no_prefix)
      }
    }

    axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
      setUserInfo(res.data);

      if (res.data.user_approval_role) {
        setUserApproval(res.data.user_approval_role);
      }
    })
  }, [])

  const currentFinantialYear = () => {
    const currentDate = moment();

    // Determine the financial year
    const currentYear = currentDate.year();
    const fiscalYearStartMonth = 6; // Assuming April as the start month for the financial year
    const fiscalYear = `${currentYear - 2} - ${currentYear - 1}`;

    return `${fiscalYear}`
  }

  return (
    <>
      <ModalComponent
        show={IsShowModal}
        handleClose={() => setIsShowModal(false)}
        handleShow={() => setIsShowModal(true)}
        modalTitle="Add Patient"
      >
        <form onSubmit={AddPatientSubmit}>
          <div className="form-group">
            <label>Identification No. <span className='text-danger'>*</span> </label>
            <div className="row px-3">
              <select className="form-control col-3" onChange={(e) => setPaitientIdentificationType(e.target.value)} required>
                <option value="">Type</option>
                <option value="BA">BA</option>
                <option value="No">No</option>
                <option value="TSO">TSO</option>
                <option value="CS">CS</option>
                <option value="MS">MS</option>
                <option value="MES">MES</option>
              </select>
              <input type="text" onChange={(e) => setPaitientIdentificationNumber(e.target.value)} required className="form-control col-9" name="number" placeholder="Identification Number" />

            </div>

          </div>
          <div className="form-group">
            <label>Patient Name <span className='text-danger'>*</span></label>
            <input type="text" onChange={(e) => setPaitientName(e.target.value)} required className="form-control" name="name" placeholder="Patient Name" />
          </div>
          <div className="form-group">
            <div className="row px-1">
              <div className="col-6">
                <label>Relation <span className='text-danger'>*</span></label>
                <select className="form-control" onChange={(e) => setPaitientIdentificationRelation(e.target.value)} required>
                  <option value="">Relation</option>
                  <option value="Self">Self</option>
                  <option value="W/O">W/O</option>
                  <option value="D/O">D/O</option>
                </select>
              </div>
              <div className="col-6">
                <label>Unit <span className='text-danger'>*</span></label>
                <select className="form-control" onChange={(e) => setPaitientIdentificationUnit(e.target.value)}>
                  <option value="">Unit</option>
                  {PatientUnits.map((val, key) => (
                    <option key={key} value={val.id}>{val.name}</option>
                  ))}
                </select>
              </div>
              {IsPatientError && <span className="alert alert-danger col-12 mt-1">Identification Number Exists with this relation!</span>}
            </div>
          </div>
          <div className="text-center">
            <button type="submit" className='btn btn-success' disabled={IsPatientError}>
              Add Patient
            </button>
          </div>
        </form>
      </ModalComponent>
      <ModalComponent
        show={IsShowSaveTemplateModal}
        handleClose={() => setIsShowSaveTemplateModal(false)}
        handleShow={() => setIsShowSaveTemplateModal(true)}
        modalTitle="Save demand as a Template"
      >
        <form onSubmit={DemandSaveTemplateSubmit}>
          <div className="form-group">
            <label>Template Name <span className='text-danger'>*</span></label>
            <input type="text" onChange={(e) => setTemplateName(e.target.value)} required className="form-control" name="templatename" placeholder="Template Name" />
          </div>
          <div className="text-center">
            <button type="submit" className='btn btn-success' disabled={isFormSavingasTemplate}>
              {isFormSavingasTemplate ? 'Saving...' : 'Save As Template'}
            </button>
          </div>
        </form>
      </ModalComponent>

      <form onSubmit={handleDemandPVMSSubmit}>
        <div className="row">
          {userInfo && userInfo.sub_organization && userInfo.sub_organization.id != 2 && !demandId && userApproval?.role_key == 'cmh_clark' &&
            <div className='my-2 col-9'>
              <AsyncSelect cacheOptions loadOptions={loadDemandTemplateOptions} onChange={handleSelectDemadTemplate} defaultOptions placeholder="Load Demand from Template" />
            </div>
          }
          {demandId &&
            <div className='my-2 col-6'>
              Demand No:
              <input className='form-control' type='text' onChange={(e) => setDemandNo(e.target.value)} value={DemandNo} required readOnly={IsPublished} />
              {DemandNoError && <span className='text-danger'>{DemandNoError}</span>}
            </div>
          }
          <div className="col-lg-12">
            <div className='row mb-3'>
              <div className='col-md-9'>

                <div className='row'>
                  <div className='col-6 mb-2'>

                    {!demandId && <>
                      <b>Demand Date: </b>
                      {/* <input type="date" pattern="\d{2}-\d{2}-\d{4}" className="form-control" value={demandDate} onChange={(e)=> setDemandDate(e.target.value)}/> */}
                      <div>
                        <DatePicker
                          className="form-control"
                          selected={demandDate}
                          onChange={(date) => setDemandDate(date)}
                          dateFormat="dd/MM/yyyy"
                        />
                      </div>
                    </>
                    }
                  </div>
                  <div className='col-6 mb-2'>
                    {!demandId && <>
                      {demandDate && <>
                        <b>Demand No:<span className='text-danger'>*</span>  <label><input type='checkbox' onChange={(e) => { setIsCustomDemandNo(e.target.checked); setDemandNo('') }} checked={isCustomDemandNo} /> Custom number</label> </b>
                        {!isCustomDemandNo ? <PatternFormat value={DemandNo} format={`##.##.###.###.##.###.##.${moment(demandDate).format('DD')}.${moment(demandDate).format('MM')}.${moment(demandDate).format('YYYY')}`}
                          allowEmptyFormatting
                          mask="_" className='form-control'
                          onChange={(e) => setDemandNo(e.target.value)}
                        />
                          :
                          <input className='form-control' type='text' onChange={(e) => setDemandNo(e.target.value)} value={DemandNo} required />
                        }
                      </>}
                      {DemandNoError && <span className='text-danger'>{DemandNoError}</span>}
                    </>
                    }

                  </div>
                  {(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2) ? <div className='col-6 mb-2'>
                    <b>Financial Year:<span className='text-danger'>*</span> </b>
                    <select onChange={(e) => setFy(e.target.value)} value={fy} className='form-control' required={(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2)} disabled={window.demand_id}>
                      <option value="">Select</option>
                      {financialYears.map((val, key) => (
                        <option key={key} value={val.id}>{val.name}</option>
                      ))}
                    </select>
                  </div>
                    : <div className='col-6 mb-2'>
                      <b>Demand Types:<span className='text-danger'>*</span> </b>
                      <select onChange={(e) => handleChangeDemandType(e)} value={demadType} className='form-control' required={!(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2)} disabled={window.demand_id}>
                        <option value="">Select</option>
                        {demandTypes.map((val, key) => (
                          <option key={key} value={val.id}>{val.name}</option>
                        ))}
                      </select>
                    </div>}

                  <div className='col-6 mb-2'>
                    <b>Item Type:<span className='text-danger'>*</span> </b>

                    {demandId ?
                      <select
                        onChange={(e) => handleChangeItemType(e)} value={DemandItemType?.id}
                        className='form-control' required disabled={demadType == 4}>
                        <option value="">Select Item Type</option>
                        {ItemTypeList && ItemTypeList.map((val, key) => (
                          <>
                            {val.name != 'Dental' && <option key={key} value={val.id}>{val.name}</option>}
                          </>
                        ))}
                      </select>
                      :
                      <div>
                        {DemandItemTypeMultiple.length == 0 ?
                          'No Pvms Selected.'
                          :
                          <>{DemandItemTypeMultiple.map((val, index) => val.name + (DemandItemTypeMultiple.length != (index + 1) ? ', ' : ''))}</>
                        }

                      </div>
                    }

                  </div>
                  <div className='col-6 mb-2'>
                    <select
                      onChange={(e) => setDemandCategory(e.target.value)} value={DemandCategory}
                      className='form-control'>
                      <option value="">Select Demand Category</option>
                      <option value="IMM">IMM</option>
                      <option value="FLASH">FLASH</option>
                      <option value="PRI">PRI</option>
                    </select>
                  </div>

                  <div className='col-12 mb-2'>
                    <div className='mb-2'>
                      <b>Top Description: </b>
                      <CKEditor
                        editor={ClassicEditor}
                        config={{
                          toolbar: ['undo', 'redo',
                            '|', 'heading',
                            '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                            '|', 'blockQuote', 'insertTable', 'underline',
                            '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'alignment']
                        }}
                        data={description}
                        onChange={(event, editor) => {
                          const data = editor.getData();
                          setDescription(data)
                        }}
                      />
                      {/* <textarea name="description" value={description} onChange={(e) => setDescription(e.target.value)} className='form-control'></textarea> */}
                    </div>
                  </div>

                  {(DemandItemType?.id == 3 || DemandItemType?.id == 1 || DemandItemType?.id == 5) &&
                    <div className='col-md-9'>
                      <b>Is Dental Type : </b><br />
                      <label htmlFor="">
                        <input type='radio' name='is_dental_type' onChange={(e) => setIsDentalType(e.target.checked)} checked={isDentalType} /> Yes {" "}
                        <input type='radio' name='is_dental_type' onChange={(e) => setIsDentalType(!e.target.checked)} checked={!isDentalType} /> No
                      </label>
                    </div>
                  }
                </div>
              </div>
              <div className='col-md-3 text-right'>
                {demadType == 1 && <button onClick={() => setIsShowModal(true)} type="button" className='btn btn-success'>
                  <i className="fa fa-plus btn-icon-wrapper"></i>
                  {" "}Add Patient
                </button>}
              </div>
              <div className='col-12 mb-2'>
                <div className='mb-2'>
                  <b>Bottom Description: </b>
                  <CKEditor
                    editor={ClassicEditor}
                    config={{
                      toolbar: ['undo', 'redo',
                        '|', 'heading',
                        '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                        '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                        '|', 'blockQuote', 'insertTable', 'underline',
                        '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'alignment']
                    }}
                    data={description1}
                    onChange={(event, editor) => {
                      const data = editor.getData();
                      setDescription1(data)
                    }}
                  />
                  {/* <textarea name="description" value={description} onChange={(e) => setDescription(e.target.value)} className='form-control'></textarea> */}
                </div>
              </div>
              <div className='col-md-12'>
                <div>
                  <b>Upload Document: </b>
                  <input type="file" name='document' multiple id='document' onChange={(e) => handleChangeFile(e.target.files)} />
                  {renderFileList()}
                  <br />
                  {demandId && documentFiles &&
                    documentFiles.map((item, index) => (
                      <a className='pr-2' href={`${window.app_url}/storage/demand_documents/${item.file}`} target='_blank'>{index + 1}. <i className='fa fa-download'></i> Uploaded Document</a>
                    ))
                  }
                </div>
              </div>
            </div>


            {demadType == 4 ?
              <table className='table'>
                <thead>
                  <tr>
                    <th>Sl</th>
                    <th>PVMS.No</th>
                    <th className='text-center'>Date Received</th>
                    <th className='text-center'>Date of Installed</th>
                    {/* <th className='text-center'>Warranty Date</th> */}
                    <th className='text-center'>Supplier</th>
                    <th className='text-center width-250'></th>
                    <th className='text-center'>Remarks</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {repairPVMS.map((val, key) => (
                    <tr>
                      <td>
                        {key + 1}
                      </td>
                      <td>
                        {val.pvms_id}
                      </td>
                      <td>
                        <DatePicker
                          className="form-control"
                          selected={val.issue_date}
                          name='issue_date'
                          onChange={(date) => handleDemandRepairDateValueChange(date, key, 'issue_date')}
                          dateFormat="dd/MM/yyyy"
                        />
                      </td>
                      <td>
                        <DatePicker
                          className="form-control"
                          selected={val.installation_date}
                          name='installation_date'
                          onChange={(date) => handleDemandRepairDateValueChange(date, key, 'installation_date')}
                          dateFormat="dd/MM/yyyy"
                        />
                      </td>
                      {/* <td>
                    <DatePicker
                        className="form-control"
                        selected={val.warranty_date}
                        name='warranty_date'
                        onChange={(date) => handleDemandRepairDateValueChange(date, key, 'warranty_date')}
                        dateFormat="dd/MM/yyyy"
                    />
                  </td> */}
                      <td>
                        <input type='text' className='form-control' name='supplier' value={val.supplier} onChange={(e) => handleDemandRepairValueChange(e.target, key)} />
                      </td>
                      <td>
                        <div className='row'>
                          <div className='col-6'>
                            Auth <br /><input type='number' className='form-control' name='authorized_machine' value={val.authorized_machine} onChange={(e) => handleDemandRepairValueChange(e.target, key)} />
                          </div>
                          <div className='col-6'>
                            Held <br /><input type='number' className='form-control' name='existing_machine' value={val.existing_machine} onChange={(e) => handleDemandRepairValueChange(e.target, key)} />
                          </div>
                          <div className='col-6'>
                            Running <input type='number' className='form-control' name='running_machine' value={val.running_machine} onChange={(e) => handleDemandRepairValueChange(e.target, key)} />
                          </div>
                          <div className='col-6'>
                            Unserviceable <input type='number' className='form-control' name='disabled_machine' value={val.disabled_machine} onChange={(e) => handleDemandRepairValueChange(e.target, key)} />
                          </div>
                        </div>

                      </td>
                      <td>
                        <textarea className='form-control' name='remarks' value={val.remarks} onChange={(e) => handleDemandRepairValueChange(e.target, key)}></textarea>
                      </td>
                    </tr>
                  ))}

                </tbody>
              </table>
              :
              <table className='table'>
                <thead>
                  <tr>
                    <th>Sl</th>
                    {demadType == 1 && <th className='text-center'>Patient Name <span className='text-danger'>*</span></th>}
                    <th>PVMS.No</th>
                    <th className='text-center'>Nomenclature</th>
                    {demadType == 1 && <th className='text-center'>Disease <span className='text-danger'>*</span></th>}
                    <th className='text-center'>Item Type</th>
                    <th className='text-center'>A/U</th>
                    {(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2) &&
                      <>
                        <th className='text-center'>
                          Purchase {currentFinantialYear()}
                        </th>
                        <th className='text-center'>Present Stock</th>
                        <th className='text-center'>Proposed Reqr</th>
                      </>
                    }
                    {DemandItemType?.id == 1 && <th className='text-center'></th>}
                    <th className='text-right width-150'>Qty. Req.<span className='text-danger'>*</span></th>
                    <th className='text-center'>Remarks</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {demandPVMS.map((val, key) => (
                    <tr key={key}>
                      <td>
                        {key + 1}
                      </td>
                      {demadType == 1 &&
                        <td>
                          <textarea type="text" required={demadType == 1} className="form-control" name="patient_name"
                            value={val.patient_name}
                            onChange={(e) => handleDemandPVMSValueChange(e.target, key)}
                            onFocus={() => setCurrentPatientIndex(key)}
                            onBlur={() => setTimeout(() => setCurrentPatientIndex(), 500)}
                            autoComplete='off' />
                          {(currentPatientIndex == key && PatientList.length > 0) && <InputSearch data={PatientList} onSelect={(s) => handleSelectPatient(s, key)} />}
                        </td>
                      }
                      <td>
                        {val.pvms_id}
                      </td>
                      <td className='text-center'>
                        {val.nomenclature}
                      </td>
                      {demadType == 1 &&
                        <td>
                          <input type="text"
                            required={demadType == 1}
                            className="form-control"
                            name="disease"
                            value={val.disease}
                            onChange={(e) => handleDemandPVMSValueChange(e.target, key)}
                            onFocus={() => setCurrentDiseaseIndex(key)}
                            onBlur={() => setTimeout(() => setCurrentDiseaseIndex(), 500)}
                            autoComplete='off' />
                          {(currentDiseaseIndex == key && diseaseList.length > 0) && <InputSearch data={diseaseList} onSelect={(s) => handleSelectDisease(s, key)} />}
                        </td>
                      }
                      <td className='text-center'>
                        {val.item_type}
                      </td>
                      <td className='text-center'>
                        {val.au}
                      </td>
                      {(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2) &&
                        <>
                          <td>
                            <input type="number" required className="form-control text-right" name="prev_purchase" value={val.prev_purchase} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                          </td>
                          <td><input type="number" required className="form-control text-right" name="present_stock" value={val.present_stock} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} /></td>
                          <td><input type="number" required className="form-control text-right" name="proposed_reqr" value={val.proposed_reqr} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} /></td>
                        </>
                      }
                      {DemandItemType?.id == 1 &&
                        <td>
                          <div className='row s5'>
                            <div className='col-6'>
                              Auth <br /><input type='number' className='form-control' name='authorized_machine' value={val.authorized_machine} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                            </div>
                            <div className='col-6'>
                              Held <br /><input type='number' className='form-control' name='existing_machine' value={val.existing_machine} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                            </div>
                            <div className='col-6'>
                              Svc <input type='number' className='form-control' name='running_machine' value={val.running_machine} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                            </div>
                            <div className='col-6'>
                              Unsvc <input type='number' className='form-control' name='disabled_machine' value={val.disabled_machine} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                            </div>
                            <div className='col-12'>
                              Dept/Ward <input type='text' className='form-control' name='ward' value={val.ward} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                            </div>
                          </div>
                        </td>
                      }
                      <td>
                        <input type="number" required className="form-control text-right" name="qty" value={val.qty} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                      </td>
                      <td>
                        <textarea className='form-control' name='remarks' value={val.remarks} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} ></textarea>
                      </td>
                      <td>
                        <button className='btn' type="button" onClick={() => handleDeletePVMS(key)}>
                          <i className='pe-7s-close-circle text-danger f20 font-weight-bold'></i>
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            }

            {(demadType != 4 && demandPVMS.length == 0) || (demadType == 4 && repairPVMS.length == 0) ?
              <div className='text-center'>No Pvms Added</div>
              :
              <></>
            }

            <div className='row my-3'>
              <div className='col-md-12 gap-2'>
               <b className='mb-2 text-danger'> PLEASE CREATE SEPARATE DEMAND FOR CONTROL OR NON-CONTROL TYPE ITEMS</b>
              </div>
              <div className='col-md-12 gap-2'>

                <b className='mb-2'>Search PVMS : {DemandItemType && <>{DemandItemType.name} PVMS items for Demand</>}</b>
                <AsyncSelect
                 cacheOptions
                 loadOptions={loadOptions}
                 onChange={handleChangePvms}
                 value={''}
                 defaultOptions
                 placeholder="PMVS No"
                />
              </div>

            </div>
            <div className='d-flex justify-content-end gap-2'>
              {(userApproval?.role_key == 'cmh_clark' && demadType == 4) &&
                <div className="position-relative custom-control custom-checkbox mb-2">
                  <input name="check" id="exampleCheck" type="checkbox" checked={IsPublished == 1} onChange={(e) => {
                    if (e.target.checked) {
                      setIsPublished(1)
                    } else {
                      setIsPublished(0)
                    }
                  }} className="custom-control-input" />
                  <label for="exampleCheck" class="custom-control-label font-weight-bold f16 pr-2">Checked & Forward for JCO approval.</label>
                </div>
              }

              {(demadType != 4 && demandPVMS.length == 0) || (demadType == 4 && repairPVMS.length == 0) ?
                <div className='text-center'></div>
                :
                <div className='text-right'>
                  {userInfo && userInfo.sub_organization && userInfo.sub_organization.id != 2 && userApproval?.role_key == 'cmh_clark' &&
                    <button type='button' className='btn btn-primary mr-2' onClick={() => setIsShowSaveTemplateModal(true)} disabled={isFormSavingasTemplate}>
                      {isFormSavingasTemplate ? 'Saving...' : 'Save as Template'}
                    </button>
                  }

                  {userApproval?.role_key == 'mo' &&
                    <div className="position-relative custom-control custom-checkbox mb-2">
                      <input name="check" id="exampleCheck" type="checkbox" checked={IsPublished == 1} onChange={(e) => {
                        if (e.target.checked) {
                          setIsPublished(1)
                        } else {
                          setIsPublished(0)
                        }
                      }} className="custom-control-input" />
                      <label for="exampleCheck" class="custom-control-label font-weight-bold f16">Checked & Forward for OIC approval.</label>
                    </div>
                  }
                  <button className='btn btn-success' disabled={isFormSubmited || DemandNoChecking}>{isFormSubmited ? 'Saving...' : 'Save & Forward'}</button>
                </div>
              }
            </div>



          </div>
        </div>
      </form>
    </>

  )
}


if (document.getElementById('react-demand')) {
  createRoot(document.getElementById('react-demand')).render(<CreateEdit />)
}
