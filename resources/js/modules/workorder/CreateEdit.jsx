import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Swal from 'sweetalert2';

export default function CreateEdit() {

  const [selectedVendor, setSelectedVendor] = useState();
  const [contractNumber, setContractNumber] = useState();
  const [contactDate, setContactDate] = useState();
  const [lastSubmitDate, setLastSubmitDate] = useState();
  const [workorderPvms, setWorkorderPvms] = useState([]);
  const [financialYears, setFinancialYears] = useState([]);
  const [financialYear, setFinancialYear] = useState([]);
  const [workorderId, setWorkorderId] = useState();
  const [WorkorderDetails, setWorkorderDetails] = useState('')
  const [WorkorderBottomDetails, setWorkorderBottomDetails] = useState('')
  const [isMunirKeyboard, setIsMunirKeyboard] = useState(false)
  const [selectedFiles, setSelectedFiles] = useState([])
  const [documentFiles, setDocumentFiles] = useState()

  useEffect(() => {
    axios.get(`${window.app_url}/settings/financial-years/api`)
      .then((res) => {
        setFinancialYears(res.data)
      })

    const workorder_edit_id = document.getElementById('workorder-id')?.getAttribute('data-id');

    if(workorder_edit_id){
      setWorkorderId(workorder_edit_id);

      axios.get(`${window.app_url}/workorder/details-json/${workorder_edit_id}`)
      .then((res) => {
        const data = res.data;

        console.log(data.documents);

        setFinancialYear(data.financial_year_id);
        setContractNumber(data.contract_number)
        setContactDate(new Date(data.contract_date))
        setLastSubmitDate(new Date(data.last_submit_date))
        setSelectedVendor(data.vendor)
        setWorkorderDetails(data.notesheet_details)
        setWorkorderBottomDetails(data.notesheet_details1)
        setDocumentFiles(data.documents)
        if(data.is_munir_keyboard) {
            handleChangeIsMunirKeyboard(data.is_munir_keyboard)
        }
        setIsMunirKeyboard(data.is_munir_keyboard)

        console.log(data.vendor);

        let workorder_pvms_data = [];

        for (const iterator of data.workorder_pvms) {
          workorder_pvms_data.push({
            id: iterator.id,
            pvms_id: iterator.pvms_id,
            nomenclature: iterator.pvms?.nomenclature,
            au: iterator.pvms?.unit_name?.name,
            qty: iterator.qty,
            unit_price: iterator.unit_price,
            remarks: iterator.pvms_id,
            delivery_mood: iterator.delivery_mood
          })
        }

        setWorkorderPvms(workorder_pvms_data)
      })
    }

  }, [])

  const handleChangePvmsVal = (target, csr, index) => {
    workorderPvms[index] = { ...workorderPvms[index], [target.name]:target.value }
    setWorkorderPvms([...workorderPvms])
  }

  const loadVendorOptions = (inputValue, callback) => {
    axios.get(window.app_url + '/all/vendor-json?keyword=' + inputValue).then((res) => {
      const data = res.data;

      let option = [];
      for (const iterator of data) {
        option.push({ value: iterator.id, label: iterator.name, data: iterator })
      }

      callback(option);
    })
  };

  const handleChangeSelectVendor = (item, select) => {
    setSelectedVendor(item.data)

  }

  const handleChangeIsMunirKeyboard = (value) => {
        setIsMunirKeyboard(value)
        if(value){
            window.$('.ck-editor__main').addClass('munir-bangla')
        } else{
            window.$('.ck-editor__main').removeClass('munir-bangla')
        }

    }

  const loadOptions = (inputValue, callback) => {
    axios.get(window.app_url+'/settings/pvms/search?keyword='+inputValue).then((res)=>{
      const data = res.data;

      let option=[];
      for (const iterator of data) {
        option.push({value:iterator.id, label:iterator.pvms_id+' - '+iterator.nomenclature+' - '+ (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data:iterator})
      }

      callback(option);
    })
  };

  const handleChangePvms = (value) => {
    const pvms_exists = workorderPvms.find(i => i.pvms_id==value.value)

    if(pvms_exists) {


    } else {
      const { id,nomenclature,unit_name, pvms_id, item_typename } = value.data

      const new_demand_pvms = {
        id: null,
        pvms_id: id,
        nomenclature,
        au:unit_name?.name,
        qty:'',
        unit_price:'',
        remarks:''
      }

      setWorkorderPvms((prev) => {
        let copy = [...prev]
        copy.push(new_demand_pvms)

        return copy
      })
    }
  }

  const handleChangeFile = (files) => {

    for (let index = 0; index < files.length; index++) {
        const file = files[index];
        if((file.size / 1024 / 1024) > 10){
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

  const uploadDocumentFiles = async (workorder_id_input) => {
      const data = new FormData();
      data.append('workorder_id', workorder_id_input)
      for (let i = 0; i < selectedFiles.length; i++) {
        data.append('document_files[]', selectedFiles[i]);
      }
      await axios.post(window.app_url+'/workorder-update-document', data)
  }

  const handleSubmit = (e) => {
    e.preventDefault();

    if(!WorkorderDetails || !WorkorderBottomDetails) {
        Swal.fire({
            icon: 'error',
            // title: 'Oops...',
            text: `Workorder Details is required!`,
            // footer: '<a href="">Why do I have this issue?</a>'
        })
        return;
    }

    const requestData = {
      vandor_id: selectedVendor.id,
      contract_number: contractNumber,
      last_submit_date: lastSubmitDate,
      contact_date: contactDate,
      financial_year: financialYear,
      workorder_csr_pvms: workorderPvms,
      'notesheet_details': WorkorderDetails,
      'notesheet_details1' : WorkorderBottomDetails,
      'is_munir_keyboard' : isMunirKeyboard,
    }

    if(workorderId){
      axios.put(`${window.app_url}/workorder/${workorderId}`, requestData)
        .then((res) => {
          if(selectedFiles.length>0){
            uploadDocumentFiles(workorderId);
            window.location.href = '/workorder'
          }else{
            window.location.href = '/workorder'
          }
        })
    }else{
      axios.post(`${window.app_url}/workorder`, requestData)
        .then((res) => {
          if(selectedFiles.length>0){
            uploadDocumentFiles(res.data.id);
            window.location.href = '/workorder'
          }else{
            window.location.href = '/workorder'
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

        if(workorderId && workorderPvms[index]) {
          axios.post(window.app_url+`/workorder-pvms-remove`, {workorder_pvms_id:workorderPvms[index].id}).then((res) => {
            console.log(res.data);

          }).catch(() => {

          })
        }

        setWorkorderPvms((prev)=> {
          let copy = [...prev]

          return copy.filter((val, key) => {
            if(index!=key){
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

      }
    })
  }

  return (
    <div>
      <form onSubmit={handleSubmit}>
        <div className="col-lg-12">
          <div className="row">
            <div className="col-lg-6">
              <div className="form-group">
                <label>Company Name <span className="requiredStar">*</span></label>
                <AsyncSelect cacheOptions name='vendor_id' loadOptions={loadVendorOptions} onChange={handleChangeSelectVendor} value={{value: selectedVendor?.id, label:selectedVendor?.name, data:selectedVendor}} placeholder="Company Name" defaultOptions required />
              </div>

              <div className="form-group">
                <label>Last Submit Date <span className="requiredStar">*</span></label>
                <DatePicker
                    className="form-control"
                    selected={lastSubmitDate}
                    onChange={(date) => setLastSubmitDate(date)}
                    dateFormat="dd/MM/yyyy"
                />
                {/* <input type="date" required className="form-control" name="last_submit_date" value={lastSubmitDate} onChange={(e) => setLastSubmitDate(e.target.value)} /> */}
              </div>
              <div className="form-group">
                <label>Financial Years <span className="requiredStar">*</span></label>
                <select className='form-control' required value={financialYear} onChange={(e) => setFinancialYear(e.target.value)}>
                  <option value="">Select</option>
                  {financialYears.map((val, key) => (
                    <option key={key} value={val.id}>{val.name}</option>
                  ))}
                </select>
              </div>

            </div>
            <div className="col-lg-6">
              <div className="form-group">
                <label>Contract Number <span className="requiredStar">*</span></label>
                <input type="text" required className="form-control" name="contact_number" value={contractNumber} onChange={(e) => setContractNumber(e.target.value)} />
              </div>
              <div className="form-group">
                <label>Contract Date <span className="requiredStar">*</span></label>
                <DatePicker
                    className="form-control"
                    selected={contactDate}
                    onChange={(date) => setContactDate(date)}
                    dateFormat="dd/MM/yyyy"
                />
                {/* <input type="date" required className="form-control" name="contract_date" value={contactDate} onChange={(e) => setContactDate(e.target.value)} /> */}
              </div>
              <div className="form-group">
                <label>Address <span className="requiredStar">*</span></label>
                <div>{selectedVendor?.address ? selectedVendor?.address : 'Select Compnay First'}</div>
              </div>
              <div className="form-group">
                <label>Contact Number <span className="requiredStar">*</span></label>
                <div>{selectedVendor?.phone ? selectedVendor?.phone : 'Select Compnay First'}</div>
              </div>
            </div>
            <div className='col-12 my-2'>
                <div>
                    Workorder Top Details <span className="text-danger">*</span>{' '}
                    <input type='checkbox' onChange={(e) => handleChangeIsMunirKeyboard(e.target.checked)} checked={isMunirKeyboard}/> Munir Keyboard
                </div>
                <div className='mt-2'>
                    <CKEditor
                        editor={ ClassicEditor }
                        config={{toolbar:  [  'undo', 'redo',
                        '|', 'heading',
                        '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                        '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                        '|', 'blockQuote', 'insertTable','underline',
                        '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent','alignment']}}
                        data={WorkorderDetails}
                        onChange={ ( event, editor ) => {
                            const data = editor.getData();
                            setWorkorderDetails(data)
                        } }
                        required
                    />
                </div>
                <div className='mt-2'>
                    Workorder Bottom Details <span className="text-danger">*</span>{' '}
                </div>
                <div className='mt-2'>
                    <CKEditor
                        editor={ ClassicEditor }
                        config={{toolbar:  [  'undo', 'redo',
                        '|', 'heading',
                        '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                        '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                        '|', 'blockQuote', 'insertTable','underline',
                        '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent','alignment']}}
                        data={WorkorderBottomDetails}
                        onChange={ ( event, editor ) => {
                            const data = editor.getData();
                            setWorkorderBottomDetails(data)
                        } }
                        required
                    />
                </div>
            </div>

            <div className='col-md-12 mb-3'>
                <div>
                  <b>Upload Document: </b>
                  <input type="file" name='document' multiple id='document' onChange={(e) => handleChangeFile(e.target.files)} />
                  {renderFileList()}
                  <br/>
                  {workorderId && documentFiles &&
                  documentFiles.map((item,index) => (
                    <a className='pr-2' href={`${window.app_url}/storage/workorder_documents/${item.file}`} target='_blank'>{index+1}. <i className='fa fa-download'></i> Uploaded Document</a>
                  ))
                  }
                </div>
            </div>

            <div className="col-lg-12">
              <table className="table">
                <thead>
                  <tr>
                    <th>Sl</th>
                    <th>PVMS</th>
                    <th>Nomenclature</th>
                    <th>Specification</th>
                    <th>A/U</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                    <th>Delivery Mode</th>
                    <th>Remarks</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {workorderPvms.map((val, key) => (
                    <tr key={key}>
                      <td>{key + 1}</td>
                      <td>{val.pvms_id}</td>
                      <td>{val.nomenclature}</td>
                      <td>{val.details}</td>
                      <td>{val.au}</td>
                      <td>
                        <input type='text' className='form-control' name='qty' value={val.qty} onChange={(e) => handleChangePvmsVal(e.target, val, key)} autoComplete='false' required/>
                      </td>
                      <td>
                        <input type='text' className='form-control' name='unit_price' value={val.unit_price} onChange={(e) => handleChangePvmsVal(e.target, val, key)} autoComplete='false' required/>
                      </td>
                      <td>{(val.qty * val.unit_price).toFixed(2)}</td>
                      <td>
                        <select className='form-control' required value={val.delivery_mood} onChange={(e) => handleChangePvmsVal(e.target, val, key)} name='delivery_mood'>
                          <option value="">Select</option>
                          <option value="Partial">Partial</option>
                          <option value="Quarter">Quarter</option>
                        </select>
                      </td>
                      <td>
                        <textarea className='form-control' name='remarks' value={val.remarks} onChange={(e) => handleChangePvmsVal(e.target, val, key)}></textarea>
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
            </div>

            <div className='col-md-12 gap-2'>
              <b className='mb-2'>Search PVMS : PVMS items for Workorder</b>
              <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangePvms} value={''} defaultOptions placeholder="PVMS No" />
            </div>

            <div className="col-lg-12">
              <br />
              <div className="mt-1">
                <button type="submit" className="btn btn-primary mt-1">
                  {workorderId ? 'Edit Workorder' : 'Create Workorder'}
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  )
}

if (document.getElementById('react-workorder')) {
  createRoot(document.getElementById('react-workorder')).render(<CreateEdit />)
}
