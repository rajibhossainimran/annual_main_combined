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
    const [organigationId, setOrganigationId] = useState()
    const [sendToOrgId, setSendToOrgId] = useState()
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
  const [organigations, setOrganigations] = useState([])
  const [sendToOrgs, setSendToOrgs] = useState([])
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
        option.push({ value: iterator.id, label: iterator.pvms_id + ' - ' + iterator.nomenclature + ' - ' + (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data: iterator })
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
    const pvms_exists = demandPVMS.find(i => i.id == value.value)

    if (pvms_exists && demadType != 1) {
      setDemandPVMS((prev) => {
        let copy = [...prev]
        let findPvmsIndex = copy.findIndex(item => item.id == value.value);
        if (findPvmsIndex > -1) {
          copy[findPvmsIndex].qty = parseInt(copy[findPvmsIndex].qty) + 1;
        }
        return copy
      })

    } else {
      const { id, nomenclature, unit_name, pvms_id, item_typename, authorized_equipment } = value.data
      setDemandItemTypeMultiple((prev) => {
        if (!prev.find(i => i.id == item_typename.id)) {
          prev.push(item_typename)
        }
        return prev
      })

      const new_demand_pvms = {
        id,
        pvms_id,
        nomenclature,
        au: unit_name?.name,
        item_type: item_typename.name,
        item_id: item_typename.id,
          batch_no: '',
          qty: 0,
          expire_date: new Date(),
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
        demand_pvms_id: null
      }

      if (demandId) {
        if (DemandItemType && DemandItemType.id != item_typename.id) {
          Swal.fire({
            icon: 'error',
            // title: 'Oops...',
            text: `You're choosing ${item_typename.name} type of PVMS item.But existing demand item type is ${DemandItemType.name}.`,
            // footer: '<a href="">Why do I have this issue?</a>'
          })

          return;
        }
      }

      if (demandPVMS.length == 0) {
        setDemandItemType(item_typename)
      }

      setDemandPVMS((prev) => {
        let copy = [...prev]
        copy.push(new_demand_pvms)

        return copy
      })
    }
  }

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
      pvmsChange(value)
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

  const handleExpireDateValueChange = (value, index, field) => {
      setDemandPVMS((prev) => {
          let copy = [...prev]

          copy[index] = { ...copy[index], ['expire_date']: value };

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
  const handleChangeOrganization = (e) => {
    setOrganigationId(e.target.value);
  }

  const handleDemandPVMSSubmit = (e) => {
    e.preventDefault();
    // alert('okay') ;
    const request_data = {
    organigationId:organigationId,
        send_to:sendToOrgId,
      type,
      demand_item_type_id: DemandItemType.id,
        fyear:fy,
      demandPVMS,
      is_published: IsPublished,
    voucher:DemandNo,
    }

    setIsFormSubmited(true)
      const data = new FormData();
      data.append('data', JSON.stringify(request_data))

 // alert("Submitting : " + window.app_url + '/pvms-stock-add' + "\n\nPayload:\n" + JSON.stringify(request_data, null, 2));
      axios.post(window.app_url +'/pvms-stock-add', data).then((res) => {

          console.log(res.data,'crate Issue');
          setIsFormSubmited(false)
          if(res.data.status){

              window.location.href ='/pvms-stock-add'      // window.pvmsStockAddUrl
          }
      }).catch((err) => {
           // alert(res.data) ;
          setIsFormSubmited(false)
          if (err.response?.data?.message) {
              window.scroll(0, 0);
              Swal.fire({
                  icon: 'error',
                  text: err.response?.data?.message,
              })
          }

      });

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
      expire_date: iterator.expire_date,
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
    axios.get(window.app_url + '/get-iod-organizations').then((res) => {
        setOrganigations(res.data.org)
        setSendToOrgs(res.data.send_to)
        setFinancialYears(res.data.fYear)
    })

    axios.get(window.app_url + '/get_all_item_types').then((res) => {
      setItemTypeList(res.data)
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

          <div className="col-lg-12">
              <table className='table'>
                <thead>
                  <tr>
                    <th>Sl</th>
                    <th>PVMS.No</th>
                    <th className='text-center'>Nomenclature</th>
                    <th className='text-center'>Item Type</th>
                    <th className='text-center'>A/U</th>
                  <th className='text-center'>Batch</th>
                  <th className='text-right width-150'>Qty. <span className='text-danger'>*</span></th>
                      <th className='text-center'>Expire Dt</th>

                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {demandPVMS.map((val, key) => (
                    <tr key={key}>
                      <td>
                        {key + 1}
                      </td>
                      <td>
                        {val.pvms_id}
                      </td>
                      <td className='text-left'>
                        {val.nomenclature}
                      </td>

                      <td className='text-center'>
                        {val.item_type}
                      </td>
                      <td className='text-center'>
                        {val.au}
                      </td>

                        <td>
                            <input type="text" required className="form-control text-right" name="batch_no" value={val.batch_no} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                        </td>
                      <td>
                        <input type="number" required className="form-control text-right" min={0} name="qty" value={val.qty} onChange={(e) => handleDemandPVMSValueChange(e.target, key)} />
                      </td>
                        <td>
                            <DatePicker
                                className="form-control"
                                selected={val.expire_date}
                                name='expire_date'
                                onChange={(date) => handleExpireDateValueChange(date, key, 'expire_date')}
                                dateFormat="dd/MM/yyyy"
                            />
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

            {(demadType != 4 && demandPVMS.length == 0) || (demadType == 4 && repairPVMS.length == 0) ?
              <div className='text-center'>No Pvms Added</div>
              :
              <></>
            }

            <div className='row my-3'>

              <div className='col-md-12 gap-2'>
                <b className='mb-2'>Search PVMS : {DemandItemType && <>{DemandItemType.name} PVMS items for Demand</>}</b>
                <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangePvms} value={''} defaultOptions placeholder="PVMS No" />
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
                  {/*{userInfo && userInfo.sub_organization && userInfo.sub_organization.id != 2 && userApproval?.role_key == 'cmh_clark' &&*/}
                  {/*  <button type='button' className='btn btn-primary mr-2' onClick={() => setIsShowSaveTemplateModal(true)} disabled={isFormSavingasTemplate}>*/}
                  {/*    {isFormSavingasTemplate ? 'Saving...' : 'Save as Template'}*/}
                  {/*  </button>*/}
                  {/*}*/}

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
                  <button className='btn btn-success' disabled={isFormSubmited || DemandNoChecking}>{isFormSubmited ? 'Saving...' : 'Save IN'}</button>
                </div>
              }
            </div>
          </div>
        </div>
      </form>
    </>

  )
}


if (document.getElementById('react-receivePvmsBatchStock')) {
  createRoot(document.getElementById('react-receivePvmsBatchStock')).render(<CreateEdit />)
}
