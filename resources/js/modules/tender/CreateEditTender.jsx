import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import moment from 'moment';
import { PatternFormat } from 'react-number-format';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
// import ClassicEditor from 'ckeditor5-build-classic-plus';
import DatePicker from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";

export default function CreateEditTender() {

    const [NotesheetList, setNotesheetList] = useState([])
    const [NotesheetAmountList, setNotesheetAmountList] = useState([])
    const [RequiredDocumentsList, setRequiredDocumentsList] = useState([])
    const [TenderPP, setTenderPP] = useState()
    const [TenderStartDate, setTenderStartDate] = useState()
    const [TenderDeadline, setTenderDeadline] = useState()
    const [TenderSecurityPercentage, setTenderSecurityPercentage] = useState()
    const [TenderSubmissionFile, setTenderSubmissionFile] = useState()
    const [TenderTermsConditionsFile, setTenderTermsConditionsFile] = useState()
    const [TenderRequirementFile, setTenderRequirementFile] = useState()
    const [TenderCreateDate, setTenderCreateDate] = useState()
    const [isFormSubmited, setIsFormSubmited] = useState(false)
    const [isDownloading, setIsDownloading] = useState(false)
    const [TenderNo, setTenderNo] = useState()
    const [TenderError, setTenderError] = useState('')
    const [TenderDetails, setTenderDetails] = useState('<p>১। &nbsp; &nbsp;সশস্ত্র বাহিনীতে ব্যবহারের নিমিত্তে নিম্নেবর্নিত ***** স্থানীয় মুদ্রায় জরুরী ভাবে ক্রয় করার নিমিত্তে পূর্ব অভিজ্ঞতা সম্পন্ন প্রস্ততকারী/সরবরাহকারী/ব্যবসায়ী/প্রতিষ্ঠানের নিকট থেকে অনলাইন দরপত্র আহবান করা যাচ্ছেঃ</p>')
    const [TenderNoChecking, setTenderNoChecking] = useState(false)
    const [UniqPVMS, setUniqPVMS] = useState([])
    const [NotesheetDefaultOptions, setNotesheetDefaultOptions] = useState([])
    const [RequiredDOcumentDefaultOptions, setRequiredDOcumentDefaultOptions] = useState([])
    const [IsPublished, setIsPublished] = useState(0)
    const [CustomTenderNo, setCustomTenderNo] = useState(0)

    useEffect(()=> {

        if(window.tender_id){
            axios.get(window.app_url+'/tender/api/'+window.tender_id).then((res) => {
                const data = res.data
                // setNotesheetList
                // {value:iterator.id, label:iterator.name, data:iterator}
                let options = []
                let defaultValues = []
                data.tender_notesheet.forEach(element => {
                    defaultValues.push(element.notesheet_id)
                    options.push({value:element.notesheet_id, label:element.notesheet.notesheet_id, data:element.notesheet})
                });
                setNotesheetList(defaultValues)
                setNotesheetDefaultOptions(options)

                let required_document_options = []
                let required_document_defaultValues = []
                data.required_files.forEach(element => {
                    required_document_defaultValues.push(element.required_document.id)
                    required_document_options.push({value:element.required_document.id, label:element.required_document.name, data:element.required_document})
                });
                setRequiredDocumentsList(required_document_defaultValues)
                setRequiredDOcumentDefaultOptions(required_document_options)
                // setNotesheetAmountList
                // setRequiredDocumentsList
                setTenderStartDate(new Date(data.start_date))
                setTenderDeadline(new Date(data.deadline))
                setTenderSecurityPercentage(data.performance_security_percentage)
                setTenderSubmissionFile(data.submission_file_name)
                setTenderTermsConditionsFile(data.terms_conditions_file)
                setTenderRequirementFile(data.requirements_file)
                setTenderCreateDate(new Date(data.created_at))
                setTenderNo(data.tender_no)
                setTenderDetails(data.details)
                // UniqPVMS
                setIsPublished(data.published)
                setTenderPP(data.purchase_price)
            })
        } else {
            setTenderCreateDate(new Date())
            if(window.suggested_tender_no_prefix) {
                setTenderNo(window.suggested_tender_no_prefix)
            }
            // axios.get(window.app_url+'/get_notesheet_readyfor_tender?keyword=').then((res)=>{
            //     const data = res.data;

            //     let option=[];
            //     for (const iterator of data) {
            //         option.push({value:iterator.id, label:iterator.notesheet_id, data:iterator})
            //     }

            //     setNotesheetDefaultOptions(option);
            // })
            // axios.get(window.app_url+'/get_required_documents?keyword=').then((res)=>{
            //     const data = res.data;

            //     let option=[];
            //     for (const iterator of data) {
            //         option.push({value:iterator.id, label:iterator.name, data:iterator})
            //     }

            //     setRequiredDOcumentDefaultOptions(option);
            // })
        }


    },[])

    useEffect(() => {
        if(TenderNo && !window.tender_id) {

            setTenderNoChecking(true);
            axios.get(window.app_url+'/uniq_tender/'+TenderNo).then((res)=>{
                if(res.data) {
                    setTenderError('Tender no. exists!');
                } else {
                    setTenderError('');
                }
                setTenderNoChecking(false);
            })

        }

    },[TenderNo,500])

    const loadNotesheetOptions = (inputValue, callback) => {
        axios.get(window.app_url+'/get_notesheet_readyfor_tender?keyword='+inputValue).then((res)=>{
            const data = res.data;

            let option=[];
            for (const iterator of data) {
                option.push({value:iterator.id, label:iterator.notesheet_id, data:iterator})
            }

            callback(option);
        })
    };

    const loadRequiredDocumnetOptions = (inputValue, callback) => {
        axios.get(window.app_url+'/get_required_documents?keyword='+inputValue).then((res)=>{
            const data = res.data;

            let option=[];
            for (const iterator of data) {
                option.push({value:iterator.id, label:iterator.name, data:iterator})
            }

            callback(option);
        })
    };

    const handleChangeSelectNoteSheet = (item,select) => {

        if(select.action === "remove-value" && select.removedValue) {
            setNotesheetList(prev => prev.filter(i => i !== select.removedValue.value))
            setNotesheetAmountList(prev => prev.filter(i => i.id !== select.removedValue.value))
            setUniqPVMS([])
            setNotesheetDefaultOptions(prev => prev.filter(i => i.value !== select.removedValue.value))
        } else if(select.action === "clear") {
            setNotesheetList([])
            setNotesheetAmountList([])
            setUniqPVMS([])
            setNotesheetDefaultOptions([])
        } else if(select.action === "select-option" && select.option) {
            setNotesheetList(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i == select.option.value);
                if(findIndexExist < 0) {
                    copy.push(select.option.value)
                }

                return copy;
            })
            setNotesheetDefaultOptions(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i.value == select.option.value);
                if(findIndexExist < 0) {
                    copy.push(select.option)
                }

                return copy;
            })
            setNotesheetAmountList(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i.id == select.option.value);
                if(findIndexExist < 0) {
                    copy.push({id:select.option.value,amount: select.option.data.notesheet_budget})
                }

                return copy;
            })

            let demandList = select?.option?.data?.notesheet_demand_p_v_m_s;
            let pvmsList = [];
            if(demandList && demandList.length>0) {
                demandList.forEach(item => {
                    // noteDemandsUniqPvms.push({...item,isSelected: true});
                    let findIndex = pvmsList.findIndex(i => i.pvms_id == item.pvms_id);

                    if(findIndex<0) {
                        pvmsList.push(item)
                    }
                })

                setUniqPVMS(pvmsList)
            }

        }
    }

    const handleChangeSelectRequiredDocuments = (item,select) => {
        debugger
        if(select.action === "remove-value" && select.removedValue) {
            setRequiredDocumentsList(prev => prev.filter(i => i !== select.removedValue.value))
            setRequiredDOcumentDefaultOptions(prev => prev.filter(i => i.value !== select.removedValue.value))
        } else if(select.action === "clear") {
            setRequiredDocumentsList([])
            setRequiredDOcumentDefaultOptions([])
        } else if(select.action === "select-option" && select.option) {
            setRequiredDocumentsList(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i == select.option.value);
                if(findIndexExist < 0) {
                    copy.push(select.option.value)
                }

                return copy;
            })
            setRequiredDOcumentDefaultOptions(prev => {
                let copy = [...prev]
                let findIndexExist = copy.findIndex(i => i.value == select.option.value);
                if(findIndexExist < 0) {
                    copy.push(select.option)
                }

                return copy;
            })
        }

    }

    const submitHandler = (e) => {
        e.preventDefault();
        if(TenderError) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: TenderError,
                // footer: '<a href="">Why do I have this issue?</a>'
              })
            window.scroll(0,0);
            return;
        }
        if(!TenderDetails) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: "Please enter tender details.",
                // footer: '<a href="">Why do I have this issue?</a>'
              })
            window.scroll(0,0);
            return;
        }

        if(TenderNoChecking) {
            Swal.fire({
                icon: 'info',
                // title: 'Oops...',
                text: `Please wait Tender Number checking in progress!`,
                // footer: '<a href="">Why do I have this issue?</a>'
              })
            return;
        }

        if(TenderNo.includes('_') || TenderNo.length < 1 || !TenderNo) {
            setTenderError('Please Fill Up Tender Number')
            window.scroll(0,0);
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `Please Provide Valid Tender no!`,
                // footer: '<a href="">Why do I have this issue?</a>'
              })
            return;

        }
        let data = new FormData();
        data.append('tender_no',TenderNo);
        data.append('purchase_price',TenderPP);
        data.append('start_date', `${moment(TenderStartDate).format('YYYY')}-${moment(TenderStartDate).format('M')}-${moment(TenderStartDate).format('DD')}`);
        data.append('details', TenderDetails);
        // h:mm aa
        data.append('deadline', `${moment(TenderDeadline).format('YYYY-MM-DD HH:mm:ss')}`);
        data.append('performance_security_percentage',TenderSecurityPercentage?TenderSecurityPercentage:'');
        data.append('notesheets',JSON.stringify(NotesheetList));
        data.append('required_documents',JSON.stringify(RequiredDocumentsList));
        data.append('submission_file',TenderSubmissionFile);
        data.append('terms_conditions_file',TenderTermsConditionsFile);
        data.append('requirements_file',TenderRequirementFile);
        data.append('published',IsPublished);

        setIsFormSubmited(true);
        if(window.tender_id) {
            axios.post(window.app_url+'/tender-update/'+window.tender_id, data).then((res) => {
                setIsFormSubmited(false)
                window.location.href = window.tender_url
              }).catch(() => {
                setIsFormSubmited(false)
              })
        } else {
            axios.post(window.app_url+'/tender', data).then((res) => {
                setIsFormSubmited(false)
                window.location.href = window.tender_url
              }).catch(() => {
                setIsFormSubmited(false)
              })
        }

    }

    const downloadDemoXlsx = () => {
        let data = {
            note_sheets : NotesheetList
        }
        setIsDownloading(true)
        axios.post(window.app_url+'/tender_demo_xls', data,{
            responseType: "arraybuffer"
        }).then((response) => {
            setIsDownloading(false)
            const arrayBuffer = response.data;
            const blob = new Blob([arrayBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = window.URL.createObjectURL(blob);

            // Create a temporary anchor element for downloading
            const a = document.createElement('a');
            a.href = url;
            a.download = 'tender-demo-file.xlsx';

            document.body.appendChild(a);
            a.click();

            // Clean up: Revoke the URL when it's no longer needed to free up memory
            window.URL.revokeObjectURL(url);
          }).catch(() => {

          })
    }

  return (
    <>
     <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
        <h5 className="f-14">{window.tender_id ? `Tender Edit `:'Tender Create'}</h5>
     </div>
     <div className="px-2">
        <form onSubmit={submitHandler}>
            <div className="row mt-4">
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Date</label>
                      {/* <input type="date" pattern="\d{2}-\d{2}-\d{4}" className="form-control" value={TenderCreateDate} onChange={(e)=> setTenderCreateDate(e.target.value)}/> */}
                      <div>
                        <DatePicker
                            className="form-control"
                            selected={TenderCreateDate}
                            onChange={(date) => setTenderCreateDate(date)}
                            dateFormat="dd/MM/yyyy"
                            readOnly={window.tender_id}
                        />
                      </div>
                    </div>
                </div>
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <div className='d-flex justify-content-between'>
                        <label>Tender No <span className="text-danger">*</span></label>
                        {!window.tender_id && <div className="position-relative custom-control custom-checkbox">
                            <input name="check" id="customTenderNo" type="checkbox" checked={CustomTenderNo == 1} onChange={(e) => {
                                if(e.target.checked) {
                                    setCustomTenderNo(1)
                                } else {
                                    setCustomTenderNo(0)
                                }
                            }} className="custom-control-input"/>
                            <label for="customTenderNo" class="custom-control-label font-weight-bold f14">Custom No.</label>
                        </div>}
                      </div>

                        {(window.tender_id || CustomTenderNo == 1) ?
                            <input className='form-control' type='text' value={TenderNo} onChange={(e)=> setTenderNo(e.target.value)} readOnly={window.tender_id} required/>
                            :
                            <PatternFormat value={TenderNo} format={`##.##.###.###.##.###.##.${moment(TenderCreateDate).format('DD')}.${moment(TenderCreateDate).format('MM')}.${moment(TenderCreateDate).format('YYYY')}`}
                                allowEmptyFormatting
                                mask="_" className='form-control'
                                onChange={(e) => setTenderNo(e.target.value)}
                                readOnly={window.tender_id}
                            />
                        }
                        {TenderError && <span className='text-danger'>{TenderError}</span>}
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Select Notesheets <span className="text-danger">*</span></label>
                      {window.tender_id ? <AsyncSelect isMulti value={NotesheetDefaultOptions} defaultOptions={NotesheetDefaultOptions} cacheOptions name='notesheets' loadOptions={loadNotesheetOptions} onChange={handleChangeSelectNoteSheet} placeholder="Notesheet" required/>: <AsyncSelect isMulti defaultOptions cacheOptions name='notesheets' loadOptions={loadNotesheetOptions} onChange={handleChangeSelectNoteSheet} placeholder="Notesheet" required/>}
                        {NotesheetAmountList && NotesheetAmountList.length>0 &&
                         <div>Tender Budget Amount: <b>{NotesheetAmountList.reduce((prev,curr) => prev+parseFloat(curr.amount),0).toLocaleString()}</b></div>}

                    {UniqPVMS && UniqPVMS.length>0 && <div className="d-flex flex-column">
                        <div>
                            Last Selected Notesheet PVMS List
                        </div>
                        {
                            UniqPVMS.map((item,index) => (
                                <div>{index+1}. {item?.p_v_m_s?.pvms_id} - {item?.p_v_m_s?.nomenclature} - {item?.p_v_m_s?.pvms_old_name}</div>
                            ))
                        }

                    </div>}
                    </div>
                </div>
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Tender Purchase Price (BDT) <span className="text-danger">*</span></label>
                      <input type="number" className="form-control" name="tenderPP" value={TenderPP} onChange={(e)=>setTenderPP(e.target.value)} placeholder="Tender Purchase Price" required/>
                    </div>
                </div>
            </div>
            <div className="row ">
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Tender Start Date <span className="text-danger">*</span></label>
                      {/* <input type="date" pattern="\d{2}-\d{2}-\d{4}" className="form-control" name="tenderStartDate" value={TenderStartDate} onChange={(e)=>setTenderStartDate(e.target.value)} required/> */}
                      <div>
                        <DatePicker
                            className="form-control"
                            name="tenderStartDate"
                            selected={TenderStartDate}
                            onChange={(date) => setTenderStartDate(date)}
                            dateFormat="dd/MM/yyyy"
                            autoComplete={false}
                            required
                        />
                      </div>

                    </div>
                </div>
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Tender Submission Deadline <span className="text-danger">*</span></label>
                      {/* <input type="date" pattern="\d{2}-\d{2}-\d{4}" className="form-control" name="tenderDeadline" value={TenderDeadline} onChange={(e)=>setTenderDeadline(e.target.value)} required/> */}
                      <div>
                        <DatePicker
                            className="form-control"
                            name="tenderDeadline"
                            selected={TenderDeadline}
                            onChange={(date) => setTenderDeadline(date)}
                            dateFormat="dd/MM/yyyy - h:mm aa"
                            showTimeSelect
                            autoComplete={false}
                            required
                        />
                      </div>
                    </div>
                </div>
            </div>
            <div className="row ">
                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Performance Security (BDT % of offered value)</label>
                      <input max={100} min={0} type="number" className="form-control" name="tenderSecurityPercentage" value={TenderSecurityPercentage} onChange={(e)=>setTenderSecurityPercentage(e.target.value)}/>
                    </div>
                </div>

                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label>Required Documents <span className="text-danger">*</span></label>
                      {window.tender_id ? <AsyncSelect isMulti cacheOptions loadOptions={loadRequiredDocumnetOptions} onChange={handleChangeSelectRequiredDocuments} value={RequiredDOcumentDefaultOptions} defaultOptions={RequiredDOcumentDefaultOptions} placeholder="Required Documents" name="requiredDocuments" required/> :<AsyncSelect isMulti cacheOptions loadOptions={loadRequiredDocumnetOptions} onChange={handleChangeSelectRequiredDocuments} defaultOptions placeholder="Required Documents" name="requiredDocuments" required/>}
                    </div>
                </div>
            </div>
            <div className="row ">
                <div className="col-6">
                    <div className="form-group">
                      <label className="d-flex gap-2">
                        <div>Tender Specification File {window.tender_id ? <a href={`${window.app_url}/storage/tender-submission/${TenderRequirementFile}`} target='_blank'> <i className='fa fa-download'></i> Uploaded Document</a>:<></>}</div>
                    </label>

                     <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" className="form-control" name="tender_requirement"
                            onChange={(e) => {
                                let selectedFile = e.target.files[0]
                                if (selectedFile) {
                                    let fileType = selectedFile.type;
                                    if(fileType == ".csv" || fileType == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || fileType == "application/vnd.ms-excel") {
                                        setTenderRequirementFile(selectedFile)
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            // title: 'Oops...',
                                            text: `Invalid file! Only .xlsx file accepted!`,
                                            // footer: '<a href="">Why do I have this issue?</a>'
                                        })
                                    }
                                }

                            }}
                            // required={!window.tender_id}
                        />
                    </div>
                </div>

                <div className="mx-auto col-6">
                    <div className="form-group">
                      <label className="d-flex gap-2">
                        <div>Terms & Conditions {window.tender_id ? <a href={`${window.app_url}/storage/tender-submission/${TenderTermsConditionsFile}`} target='_blank'> <i className='fa fa-download'></i> Uploaded Document</a>:<></>}</div>
                    </label>
                      <input type="file" accept="application/pdf" className="form-control" name="terms_conditions" onChange={(e)=>{
                            let selectedFile = e.target.files[0]
                            if (selectedFile) {
                                let fileType = selectedFile.type;
                                debugger
                                if(fileType == "application/pdf") {
                                    setTenderTermsConditionsFile(selectedFile)
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        // title: 'Oops...',
                                        text: `Invalid file! Only .pdf file accepted!`,
                                        // footer: '<a href="">Why do I have this issue?</a>'
                                    })
                                }
                            }

                        }}/>
                    </div>
                </div>
            </div>
            <div className="row ">


                <div className='col-6 mt-2'>
                        <div>Tender Details <span className="text-danger">*</span></div>
                        <CKEditor
                            editor={ ClassicEditor }
                            config={{toolbar:  [  'undo', 'redo',
                            '|', 'heading',
                            '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                            '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                            '|', 'blockQuote', 'insertTable','underline',
                            '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent','alignment']}}
                            data={TenderDetails}
                            onChange={ ( event, editor ) => {
                                const data = editor.getData();
                                setTenderDetails(data)
                            } }
                            required
                        />
                    </div>
                    <div className="mx-auto col-6">
                    <div className="form-group">
                      <label className="d-flex gap-2">
                        <div>Tender Submission File <span className="">(xlsx file) </span></div> {NotesheetList.length > 0 &&
                      <div className='d-flex gap-2'>

                            <a className="pl-4 pr-2 cursor-pointer text-danger" onClick={downloadDemoXlsx}>
                                <i className="fa fa-download btn-icon-wrapper"></i> {" "}
                                Download pvms xlsx file
                            </a>{" "}
                            {isDownloading &&
                                <div className="spinner-border text-success" role="status">
                                    <span className="visually-hidden"></span>
                                </div>
                            }

                        </div>}
                        </label>
                      {/* <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" className="form-control" name="submissionFile" onChange={(e)=>{
                            let selectedFile = e.target.files[0]
                            if (selectedFile) {
                                let fileType = selectedFile.type;
                                if(fileType == ".csv" || fileType == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || fileType == "application/vnd.ms-excel") {
                                    setTenderSubmissionFile(selectedFile)
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        // title: 'Oops...',
                                        text: `Invalid file! Only .xlsx file accepted!`,
                                        // footer: '<a href="">Why do I have this issue?</a>'
                                    })
                                }
                            }

                        }} required/> */}
                    </div>
                </div>
            </div>
            <div className="text-center my-2">
                <div className="position-relative custom-control custom-checkbox mb-2">
                <input name="check" id="exampleCheck" type="checkbox" checked={IsPublished == 1} onChange={(e) => {
                  if(e.target.checked) {
                      setIsPublished(1)
                  } else {
                    setIsPublished(0)
                  }
                }} className="custom-control-input"/>
                <label for="exampleCheck" class="custom-control-label font-weight-bold f16">Publish Tender</label>
                </div>
            </div>
            <div className="text-center my-2">
                {window.tender_id ? <button type='submit' disabled={isFormSubmited} className='btn btn-success'>{isFormSubmited ? 'Submitting...':'Submit' }</button>
                :
                <button type='submit' disabled={isFormSubmited} className='btn btn-success'>{isFormSubmited ? <>{IsPublished ? 'Create & Publish Tender...':'Creating Tender...'}</> : <>{IsPublished ? 'Create & Publish Tender':'Create Tender'}</>}</button>}
            </div>

        </form>

     </div>
    </>
  )
}

if (document.getElementById('react-tender')) {
    createRoot(document.getElementById('react-tender')).render(<CreateEditTender />)
}
