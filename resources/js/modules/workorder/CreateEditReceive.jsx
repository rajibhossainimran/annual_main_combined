import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import moment from 'moment';
import Swal from 'sweetalert2';

export default function CreateEdit() {

  const [selectedWorkorder, setSelectedWorkorder] = useState();
  const [contractNumber, setContractNumber] = useState(moment().valueOf());
  const [ReceiveDate, setReceiveDate] = useState('');
  const [ReceiveBy, setReceiveBy] = useState('');
  const [csrPvms, setCsrPvms] = useState([]);
  const [vendorInfo, setVendorInfo] = useState();
  const [financialYear, setFinancialYear] = useState();
  const [workorderPvms, setWorkorderPvms] = useState([]);
  const [workorderReceiveId, setWorkorderReceiveId] = useState();
  const [workorderReceiveApprovedBy, setWorkorderReceiveApprovedBy] = useState();
  const [userApprovalRoleKey, setUserApprovalRoleKey] = useState()
  const [workorderReceiveDetails, setWorkorderReceiveDetails] = useState()
  const [workorderDocuments, setWorkorderDocuments] = useState('')
  const [workorderReceiveDocuments, setWorkorderReceiveDocuments] = useState('')
  const [selectedFiles, setSelectedFiles] = useState([])
  const [visibleIndices, setVisibleIndices] = useState([0]); // Show first item by default
  const [originalPvmsList, setOriginalPvmsList] = useState([]); // for dropdown
  const [getPvmslength, setPvmsLenght] = useState([]);

  const [showEditDiv, setShowEditDiv] = useState(false);
  const [remarks, setRemarks] = useState('');





  // const getWorkorderDetails = (workorder_id, workorder_receive_details) => {
  //   axios.get(`${window.app_url}/workorder/details-json/${workorder_id}`)
  //     .then((res) => {
  //       const data = res.data;
  //       console.log(data);

  //       setWorkorderDocuments(res.data.documents)

  //       let workorder_pvms_data = [];

  //       for (const iterator of data.workorder_pvms) {
  //         let delivery_data = [{
  //           delivery_qty: null,
  //           batch_no: null,
  //           expire_date: null,
  //           batch_pvms_id: null,
  //           pvms_store_id: null,
  //           workorder_receive_pvms_id: null
  //         }]
  //         if (workorder_receive_details) {
  //           const pvms_store = workorder_receive_details.workorder_receive.pvms_store;
  //           if (pvms_store.length > 0) {
  //             delivery_data = [];
  //             for (const each_pvms_store of pvms_store) {
  //               if (each_pvms_store.pvms_id == iterator.pvms_id) {
  //                 delivery_data.push({
  //                   delivery_qty: each_pvms_store.batch.qty,
  //                   batch_no: each_pvms_store.batch.batch_no,
  //                   expire_date: new Date(each_pvms_store.batch.expire_date),
  //                   batch_pvms_id: each_pvms_store.batch.id,
  //                   pvms_store_id: each_pvms_store.id,
  //                   workorder_receive_pvms_id: each_pvms_store.workorder_receive_pvms.id
  //                 });
  //               }

  //             }
  //           } else {
  //             delivery_data = [{
  //               delivery_qty: null,
  //               batch_no: null,
  //               expire_date: null,
  //               batch_pvms_id: null,
  //               pvms_store_id: null,
  //               workorder_receive_pvms_id: null
  //             }]
  //           }
  //         }

  //         workorder_pvms_data.push({
  //           id: iterator.id,
  //           pvms_id: iterator.pvms?.pvms_id,
  //           pvms_old_name: iterator.pvms?.pvms_old_name,
  //           pvms_primary_id: iterator.pvms_id,
  //           nomenclature: iterator.pvms?.nomenclature,
  //           au: iterator.pvms?.unit_name?.name,
  //           qty: iterator.qty,
  //           unit_price: iterator.unit_price,
  //           remarks: iterator.pvms_id,
  //           delivery_mood: iterator.delivery_mood,
  //           total_received: iterator.workorder_receive_pvms.reduce((s, { received_qty }) => received_qty + s, 0),
  //           delivery_data: delivery_data
  //         })
  //       }

  //       setWorkorderPvms(workorder_pvms_data);
  //       setOriginalPvmsList(workorder_pvms_data); // this holds original full PVMS list

  //     })
  // }



  const getWorkorderDetails = (workorder_id, workorder_receive_details) => {
    axios.get(`${window.app_url}/workorder/details-json/${workorder_id}`)
      .then((res) => {
        const data = res.data;

        setPvmsLenght(data.workorder_pvms)

        setWorkorderDocuments(res.data.documents);

        let workorder_pvms_data = [];

        for (const iterator of data.workorder_pvms) {
          let delivery_data = [{
            delivery_qty: null,
            batch_no: null,
            expire_date: null,
            mfg_date: null,
            batch_pvms_id: null,
            pvms_store_id: null,
            workorder_receive_pvms_id: null
          }];

          // console.log(iterator);
          if (workorder_receive_details) {
            const pvms_store = workorder_receive_details.workorder_receive.pvms_store;
            if (pvms_store.length > 0) {
              delivery_data = [];
              for (const each_pvms_store of pvms_store) {
                if (each_pvms_store.pvms_id == iterator.pvms_id) {
                  delivery_data.push({
                    delivery_qty: each_pvms_store.batch.qty,
                    batch_no: each_pvms_store.batch.batch_no,
                    expire_date: new Date(each_pvms_store.batch.expire_date),
                    mfg_date: new Date(each_pvms_store.batch.mfg_date),
                    batch_pvms_id: each_pvms_store.batch.id,
                    pvms_store_id: each_pvms_store.id,
                    workorder_receive_pvms_id: each_pvms_store.workorder_receive_pvms.id
                  });
                }
              }
            }
          }

          const totalReceived = iterator.workorder_receive_pvms.reduce(
            (sum, { received_qty }) => sum + received_qty,
            0
          );

          const workorder_edit_id = document.getElementById('workorder-receive-id')?.getAttribute('data-id');
          if (workorder_edit_id) {
            workorder_pvms_data.push({
              id: iterator.id,
              pvms_id: iterator.pvms?.pvms_id,
              pvms_old_name: iterator.pvms?.pvms_old_name,
              pvms_primary_id: iterator.pvms_id,
              nomenclature: iterator.pvms?.nomenclature,
              au: iterator.pvms?.unit_name?.name,
              qty: iterator.qty,
              unit_price: iterator.unit_price,
              remarks: iterator.pvms_id,
              receiver_remarks: iterator?.workorder_receive_pvms[0]?.receiver_remarks,
              delivery_mood: iterator.delivery_mood,
              total_received: totalReceived,
              delivery_data: delivery_data.length > 0 ? delivery_data : [{
                delivery_qty: null,
                batch_no: null,
                expire_date: null,
                mfg_date: null,
                batch_pvms_id: null,
                pvms_store_id: null,
                workorder_receive_pvms_id: null
              }]
            });
          } else {
            //Only push if (qty - total_received) > 0
            if (iterator.qty - totalReceived !== 0) {
              workorder_pvms_data.push({
                id: iterator.id,
                pvms_id: iterator.pvms?.pvms_id,
                pvms_old_name: iterator.pvms?.pvms_old_name,
                pvms_primary_id: iterator.pvms_id,
                nomenclature: iterator.pvms?.nomenclature,
                au: iterator.pvms?.unit_name?.name,
                qty: iterator.qty,
                unit_price: iterator.unit_price,
                remarks: iterator.pvms_id,
                delivery_mood: iterator.delivery_mood,
                total_received: totalReceived,
                delivery_data: delivery_data.length > 0 ? delivery_data : [{
                  delivery_qty: null,
                  batch_no: null,
                  expire_date: null,
                  mfg_date: null,
                  batch_pvms_id: null,
                  pvms_store_id: null,
                  workorder_receive_pvms_id: null
                }]
              });
            }
          }

        }

        setWorkorderPvms(workorder_pvms_data);
        setOriginalPvmsList(workorder_pvms_data); // keep original reference
      });
  };

  useEffect(() => {
    axios.get(`${window.app_url}/workorder/json`)
      .then((res) => {

      })

    const workorder_edit_id = document.getElementById('workorder-receive-id')?.getAttribute('data-id');

    if (workorder_edit_id) {
      setShowEditDiv(true);
      setWorkorderReceiveId(workorder_edit_id)

      axios.get(window.app_url + '/workorder-receive/details-json/' + workorder_edit_id).then((res) => {
        setWorkorderReceiveDetails(res.data)
        const workorder_receive = res.data.workorder_receive;
        setContractNumber(res.data?.workorder_receive?.crv_no);
        setReceiveBy(res.data?.workorder_receive?.received_by);
        debugger
        if (res.data?.workorder_receive?.receiving_date) {
          setReceiveDate(new Date(res.data?.workorder_receive.receiving_date));
        }

        setWorkorderReceiveDocuments(workorder_receive.documents)

        getWorkorderDetails(workorder_receive.workorder_id, res.data)

        setFinancialYear(workorder_receive.workorder?.financial_year)
        setVendorInfo(workorder_receive.workorder?.vendor)
        setSelectedWorkorder(workorder_receive.workorder)
        setWorkorderReceiveApprovedBy(workorder_receive.approved_by)

      })
    }

    axios.get('/get-loged-user-approval-role').then((res) => {
      setUserApprovalRoleKey(res.data?.role_key)
    })

  }, [])

  const loadWorkorderOptions = (inputValue, callback) => {
    axios.get(window.app_url + '/workorder/json?contract_number=' + inputValue).then((res) => {
      const data = res.data;

      let option = [];
      for (const iterator of data) {
        option.push({ value: iterator.id, label: iterator.contract_number, data: iterator })
      }

      callback(option);
    })
  };

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

  const handleChangeSelectWorkorder = (item, select) => {
    setVendorInfo(item.data.vendor);
    setFinancialYear(item.data.financial_year);
    setSelectedWorkorder(item.data);

    getWorkorderDetails(item.data.id, null);
  }

  const handleOnChangeReceivePvmsData = (target, workorder_pvms_index, workorder_pvms_delivery_data_index) => {
    let delivery_data = { ...workorderPvms[workorder_pvms_index] }.delivery_data;
    delivery_data[workorder_pvms_delivery_data_index] = { ...delivery_data[workorder_pvms_delivery_data_index], [target.name]: target.value }
    workorderPvms[workorder_pvms_index] = { ...workorderPvms[workorder_pvms_index], delivery_data: delivery_data }
    setWorkorderPvms([...workorderPvms]);
  }

  const handleOnChangeReceivePvmsExpireDate = (date, workorder_pvms_index, workorder_pvms_delivery_data_index) => {
    setWorkorderPvms((prev) => {
      let delivery_data = { ...workorderPvms[workorder_pvms_index] }.delivery_data;
      delivery_data[workorder_pvms_delivery_data_index] = { ...delivery_data[workorder_pvms_delivery_data_index], expire_date: date }
      workorderPvms[workorder_pvms_index] = { ...workorderPvms[workorder_pvms_index], delivery_data: delivery_data }

      return [...prev]
    })
  }

  const handleOnChangeReceivePvmsMfgDate = (date, workorder_pvms_index, workorder_pvms_delivery_data_index) => {
    setWorkorderPvms((prev) => {
      let delivery_data = { ...workorderPvms[workorder_pvms_index] }.delivery_data;
      delivery_data[workorder_pvms_delivery_data_index] = {
        ...delivery_data[workorder_pvms_delivery_data_index],
        mfg_date: date
      };
      workorderPvms[workorder_pvms_index] = {
        ...workorderPvms[workorder_pvms_index],
        delivery_data: delivery_data
      };
      return [...prev];
    });
  };


  const handleAddMoreDeliveryData = (index) => {
    setWorkorderPvms((prev) => {
      let copy = { ...prev[index] }
      copy.delivery_data.push({
        delivery_qty: null,
        batch_no: null,
        expire_date: null
      })
      prev[index] = copy

      return [...prev]
    })
  }

  const buttonName = () => {
    if (workorderReceiveId) {
      if (workorderReceiveApprovedBy == userApprovalRoleKey) {
        return 'Save'
      }
      if (!workorderReceiveApprovedBy && userApprovalRoleKey == 'stock-control-officer') {
        return 'Approve'
      } else if (workorderReceiveApprovedBy == 'stock-control-officer' && userApprovalRoleKey == 'oic') {
        return 'Approve'
      } else if (workorderReceiveApprovedBy == 'oic' && userApprovalRoleKey == 'group-incharge') {
        return 'Approve & add to stock'
      }

    } else {
      return 'Save'
    }

  }
  const uploadDocumentFiles = async (workorder_receive_id_input) => {
    const data = new FormData();
    data.append('workorder_receive_id', workorder_receive_id_input)
    for (let i = 0; i < selectedFiles.length; i++) {
      data.append('document_files[]', selectedFiles[i]);
    }
    await axios.post(window.app_url + '/workorder-receive-update-document', data)
  }


  const handleRemarksChange = (e, index) => {
    const updatedPvms = [...workorderPvms];
    updatedPvms[index].receiver_remarks = e.target.value;
    setWorkorderPvms(updatedPvms);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    const requestData = {
      workorder_receive_id: workorderReceiveId,
      workorder_id: selectedWorkorder?.id,
      store_pvms: workorderPvms,
      // ReceiveBy,
      ReceiveDate,
      contractNumber
    }
    // console.log(requestData);

    if (workorderReceiveId) {
      axios.put(`${window.app_url}/workorder/receive/${workorderReceiveId}/update`, requestData)
        .then((res) => {
          debugger
          // console.log(`this is update`, requestData);
          uploadDocumentFiles(res.data.workorder_receive.id);
          window.location.href = '/workorder/receive'
        })
    } else {
      axios.post(`${window.app_url}/workorder/receive/store`, requestData)
        .then((res) => {
          debugger
          // console.log(requestData);
          uploadDocumentFiles(res.data.workorder_receive.id);
          window.location.href = '/workorder/receive'
        })
    }

  }

  // handle add row 

  const handleAddMoreRow = () => {
    const nextIndex = workorderPvms.findIndex((_, i) => !visibleIndices.includes(i));
    if (nextIndex !== -1) {
      setWorkorderPvms((prev) => {
        const newData = [...prev];

        const lastVisibleIndex = visibleIndices[visibleIndices.length - 1];
        const lastDelivery = prev[lastVisibleIndex]?.delivery_data?.[0] || {};

        // Copy values into first delivery_data of new row
        if (newData[nextIndex].delivery_data.length === 0) {
          newData[nextIndex].delivery_data = [{
            delivery_qty: '',
            batch_no: lastDelivery.batch_no || '',
            mfg_date: lastDelivery.mfg_date || null,
            expire_date: lastDelivery.expire_date || null,
          }];
        } else {
          newData[nextIndex].delivery_data[0] = {
            ...newData[nextIndex].delivery_data[0],
            batch_no: lastDelivery.batch_no || '',
            mfg_date: lastDelivery.mfg_date || null,
            expire_date: lastDelivery.expire_date || null,
          };
        }

        return newData;
      });

      setVisibleIndices([...visibleIndices, nextIndex]);
    }
  };


  const handleRemoveRow = (indexToRemove) => {
    if (visibleIndices.length > 1) {
      setVisibleIndices((prev) => prev.filter((i) => i !== indexToRemove));
    }
  };



  const loadPvmsOptions = (inputValue, callback) => {
    const filtered = originalPvmsList
      .filter(item =>
        item.pvms_id?.toLowerCase().includes(inputValue.toLowerCase()) ||
        item.pvms_old_name?.toLowerCase().includes(inputValue.toLowerCase())
      )
      .map(item => ({
        value: item.pvms_id,
        label: `${item.pvms_id} - ${item.pvms_old_name}`
      }));

    callback(filtered);
  };


  // handle searching pvms field 
  // const handlePvmsSelect = (selectedPvmsId, rowIndex) => {
  //   const selectedItem = originalPvmsList.find(
  //     item => item.pvms_id === selectedPvmsId
  //   );

  //   if (!selectedItem) return;

  //   const updated = [...workorderPvms];

  //   updated[rowIndex] = {
  //     ...updated[rowIndex], // keep existing batch/delivery data
  //     pvms_id: selectedItem.pvms_id,
  //     pvms_old_name: selectedItem.pvms_old_name,
  //     pvms_primary_id: selectedItem.pvms_primary_id,
  //     nomenclature: selectedItem.nomenclature,
  //     au: selectedItem.au,
  //     qty: selectedItem.qty,
  //     unit_price: selectedItem.unit_price,
  //     remarks: selectedItem.remarks,
  //     delivery_mood: selectedItem.delivery_mood,
  //     total_received: selectedItem.total_received,
  //     // Keep delivery_data unchanged
  //   };

  //   setWorkorderPvms(updated);
  // };

  // prevent same pvms id selection 
  const handlePvmsSelect = (selectedPvmsId, rowIndex) => {
    // Check if the selected ID already exists in another visible row
    const isAlreadySelected = workorderPvms.some((item, index) =>
      item.pvms_id === selectedPvmsId && index !== rowIndex && visibleIndices.includes(index)
    );

    if (isAlreadySelected) {
      Swal.fire({
        icon: 'error',
        title: 'Duplicate PVMS ID',
        text: 'This PVMS ID is already selected in another row.',
      });
      return;
    }

    const selectedItem = originalPvmsList.find(
      item => item.pvms_id === selectedPvmsId
    );

    if (!selectedItem) return;

    const updated = [...workorderPvms];

    updated[rowIndex] = {
      ...updated[rowIndex],
      pvms_id: selectedItem.pvms_id,
      pvms_old_name: selectedItem.pvms_old_name,
      pvms_primary_id: selectedItem.pvms_primary_id,
      nomenclature: selectedItem.nomenclature,
      au: selectedItem.au,
      qty: selectedItem.qty,
      unit_price: selectedItem.unit_price,
      remarks: selectedItem.remarks,
      delivery_mood: selectedItem.delivery_mood,
      total_received: selectedItem.total_received,
    };

    setWorkorderPvms(updated);
  };


  console.log(workorderPvms);

  return (

    <div>
      {showEditDiv ? (
        <div>
          <form onSubmit={handleSubmit}>
            <div className="col-lg-12">
              <div className="row">
                <div className="col-lg-6">
                  <div className="form-group">
                    <label>Contract Number <span className="requiredStar">*</span></label>
                    <AsyncSelect cacheOptions name='vendor_id' loadOptions={loadWorkorderOptions} onChange={handleChangeSelectWorkorder} value={{ value: selectedWorkorder?.id, label: selectedWorkorder?.contract_number, data: selectedWorkorder }} placeholder="Contract Number" defaultOptions required />
                  </div>
                  
                </div>
                <div className="col-lg-3">
                  <div className="form-group">
                    <label>Company Name <span className="requiredStar"></span></label>
                    <div>{vendorInfo ? vendorInfo.name : 'Select Contract Number First'}</div>
                  </div>
                </div>
                <div className="col-lg-3">
                  <div className="form-group">
                    <label>Financial Year <span className="requiredStar"></span></label>
                    <div>{financialYear ? financialYear.name : 'Select Contract Number First'}</div>
                  </div>
                </div>
                {workorderDocuments &&
                  <>
                    <div className='col-lg-6 my-2'>
                      <label>Workorder Uploaded Documents:</label>
                      <div>
                        {workorderDocuments.map((item, index) => (
                          <a className='pr-2' href={`${window.app_url}/storage/workorder_documents/${item.file}`} target='_blank'>{index + 1}. <i className='fa fa-download'></i> {item.file} </a>
                        ))}
                      </div>
                    </div>

                  </>}
                <div className='col-md-12 mb-2'>
                  <div>
                    <b>Upload Document: </b>
                    <input type="file" name='document' multiple id='document' onChange={(e) => handleChangeFile(e.target.files)} />
                    {renderFileList()}
                    <br />
                    {workorderReceiveDocuments && workorderReceiveDocuments.map((item, index) => (
                      <a className='pr-2' href={`${window.app_url}/storage/workorder_receive_documents/${item.file}`} target='_blank'>{index + 1}. <i className='fa fa-download'></i> {item.file} </a>
                    ))}
                  </div>
                </div>

                <div className='col-lg-6'>
                  <b>Receive Date: </b>
                  <DatePicker
                    className="form-control"
                    selected={ReceiveDate}
                    onChange={(date) => setReceiveDate(date)}
                    dateFormat="dd/MM/yyyy"
                    required
                  />
                </div>
                <div className='col-lg-6'>
                  <b>CRV No: </b>
                  <input className='form-control' type='text' onChange={(e) => setContractNumber(e.target.value)} value={contractNumber} required />
                </div>
                <div className='col-lg-6 d-none'>
                  <b>Receive By: </b>
                  <input className='form-control' type='text' onChange={(e) => setReceiveBy(e.target.value)} value={ReceiveBy} required />
                </div>

                <div className="col-lg-12 mt-2">
                  <table className="table">
                    <thead>
                      <tr>
                        <th>Sl</th>
                        <th>PVMS</th>
                        <th>Nomenclature</th>
                        <th>A/U</th>
                        <th>Contract Qty</th>
                        <th>Already Delivered</th>
                        <th></th>
                        <th>Receiver Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      {workorderPvms.map((val, key) => (
                        <tr key={key}>
                          <td>{key + 1}</td>
                          <td>{val.pvms_id}</td>
                          <td>{val.nomenclature}</td>
                          <td>{val.au}</td>
                          <td>{val.qty}</td>
                          {val.delivery_data.map((delivery_val, delivery_key) => (
                            <td key={delivery_key}>
                              {Number(val.total_received) - Number(delivery_val.delivery_qty)}
                            </td>
                          ))}
                          <td>
                            <table>
                              <thead>
                                <tr>
                                  <th>Delivery Qty</th>
                                  <th>Batch</th>
                                  <th>Expiry Date</th>
                                </tr>
                              </thead>
                              <tbody>
                                {val.delivery_data.map((delivery_val, delivey_key) => (
                                  <tr key={delivey_key}>
                                    <td>
                                      <input type='text' className='form-control' name='delivery_qty' value={delivery_val.delivery_qty} onChange={(e) => { handleOnChangeReceivePvmsData(e.target, key, delivey_key) }} required={delivery_val.batch_no || delivery_val.expire_date} />
                                    </td>
                                    <td>
                                      <input type='text' className='form-control' name='batch_no' value={delivery_val.batch_no} onChange={(e) => { handleOnChangeReceivePvmsData(e.target, key, delivey_key) }} required={delivery_val.delivery_qty || delivery_val.expire_date} />
                                    </td>
                                    <td>
                                      <DatePicker
                                        name='expire_date'
                                        className="form-control"
                                        selected={delivery_val.expire_date}
                                        value={delivery_val.expire_date}
                                        onChange={(date) => { handleOnChangeReceivePvmsExpireDate(date, key, delivey_key) }}
                                        dateFormat="dd/MM/yyyy"
                                        autoComplete='off'
                                        required={delivery_val.delivery_qty || delivery_val.batch_no}
                                      />
                                    </td>
                                  </tr>
                                ))}

                              </tbody>

                            </table>
                            <button type='button' className='btn btn-primary btn-sm pull-right' onClick={() => handleAddMoreDeliveryData(key)}>Add More</button>
                          </td>
                          <td><textarea rows='1' className='form-control sm' value={val.receiver_remarks} /></td>
                          {/* <td>
                      <input type='text' className='form-control' name='delivery_qty' value={val.delivery_qty} onChange={(e) => {handleOnChangeReceivePvmsData(e.target, key)}} required/>
                    </td>
                    <td>
                      <input type='text' className='form-control' name='batch_no' value={val.batch_no} onChange={(e) => {handleOnChangeReceivePvmsData(e.target, key)}} required/>
                    </td>
                    <td>
                      <DatePicker
                          name='expire_date'
                          className="form-control"
                          selected={val.expire_date}
                          value={val.expire_date}
                          onChange={(date) => {handleOnChangeReceivePvmsExpireDate(date, key)}}
                          dateFormat="dd/MM/yyyy"
                          autoComplete='off'
                      />

                    </td> */}
                        </tr>
                      ))}

                    </tbody>
                  </table>
                </div>

                <div className="col-lg-12">
                  <br />
                  <div className="mt-1">
                    <button type="submit" className="btn btn-primary mt-1">{buttonName()}</button>
                    {workorderReceiveApprovedBy == userApprovalRoleKey && <span className='ml-2 text-success'>Already Approved</span>}
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      ) : (

        <div>
          <form onSubmit={handleSubmit}>
            <div className="col-lg-12">
              <div className="row">
                <div className="col-lg-6">
                  <div className="form-group">
                    <label>Contract Number <span className="requiredStar">*</span></label>
                    <AsyncSelect cacheOptions name='vendor_id' loadOptions={loadWorkorderOptions} onChange={handleChangeSelectWorkorder} value={{ value: selectedWorkorder?.id, label: selectedWorkorder?.contract_number, data: selectedWorkorder }} placeholder="Contract Number" defaultOptions required />
                  </div>

                  {/* <div className="form-group">
                <label>Contact Number <span className="requiredStar"></span></label>
                <div>{vendorInfo ? vendorInfo.phone : 'Select Contract Number First'}</div>
              </div> */}

                </div>
                <div className="col-lg-6">
                  <div className="row">
                    <div className="col-lg-6">
                      <div className="form-group">
                        <label>Financial Year <span className="requiredStar"></span></label>
                        <div>{financialYear ? financialYear.name : 'Select Contract Number First'}</div>
                      </div>
                    </div>
                    <div className="col-lg-6">
                      <div className="form-group">
                        <label>Company Name <span className="requiredStar"></span></label>
                        <div>{vendorInfo ? vendorInfo.name : 'Select Contract Number First'}</div>
                      </div>
                    </div>
                  </div>
                  {/* <div className="form-group">
                <label>Address <span className="requiredStar"></span></label>
                <div>{vendorInfo ? vendorInfo.address : 'Select Contract Number First'}</div>
              </div> */}
                </div>
                {workorderDocuments &&
                  <>
                    <div className='col-lg-6 my-2'>
                      <label>Workorder Uploaded Documents:</label>
                      <div>
                        {workorderDocuments.map((item, index) => (
                          <a className='pr-2' href={`${window.app_url}/storage/workorder_documents/${item.file}`} target='_blank'>{index + 1}. <i className='fa fa-download'></i> {item.file} </a>
                        ))}
                      </div>
                    </div>

                  </>}
                <div className='col-md-12 mb-2'>
                  <div>
                    <b>Upload Document: </b>
                    <input type="file" name='document' multiple id='document' onChange={(e) => handleChangeFile(e.target.files)} />
                    {renderFileList()}
                    <br />
                    {workorderReceiveDocuments && workorderReceiveDocuments.map((item, index) => (
                      <a className='pr-2' href={`${window.app_url}/storage/workorder_receive_documents/${item.file}`} target='_blank'>{index + 1}. <i className='fa fa-download'></i> {item.file} </a>
                    ))}
                  </div>
                </div>

                <div className='col-lg-6'>
                  <b>Receive Date: </b>
                  <DatePicker
                    className="form-control"
                    selected={ReceiveDate}
                    onChange={(date) => setReceiveDate(date)}
                    dateFormat="dd/MM/yyyy"
                    required
                  />
                </div>
                <div className='col-lg-6 mb-3'>
                  <b>CRV No: </b>
                  <input className='form-control' type='text' onChange={(e) => setContractNumber(e.target.value)} value={contractNumber} required />
                </div>
                {/* <div className='col-lg-6 '>
              <b>Receive By: </b>
              <input className='form-control' type='text' onChange={(e) => setReceiveBy(e.target.value)} value={ReceiveBy} required />
            </div> */}

                <div className="col-12">
                  <div className="row mb-2">
                    <div className="col-md-4">
                      <div className="p-2 border rounded bg-white text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                        <div className="text-muted">Total Items : <span className='fw-bolder text-primary fs-3'>{getPvmslength.length || 0}</span></div>
                      </div>
                    </div>

                    <div className="col-md-4">
                      <div className="p-2 border rounded bg-white text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                        <div className="text-muted">Due Items : <span className='fw-bolder text-danger fs-3'>{workorderPvms.length}</span></div>
                      </div>
                    </div>

                    <div className="col-md-4">
                      <div className="p-2 border rounded bg-white text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                        <div className="text-muted">Completed Deliveries : <span className='fw-bolder text-success fs-3'>{getPvmslength.length - workorderPvms.length}</span></div>
                      </div>
                    </div>
                  </div>
                </div>




                <div className="col-lg-12 mt-2">
                  <table className="table">
                    {visibleIndices.length < workorderPvms.length && (
                      <tr>
                        <td colSpan="8">
                          <div className="d-flex justify-content-end">
                            <button
                              type="button"
                              className="btn btn-success"
                              onClick={handleAddMoreRow}
                            >
                              + Add More Item
                            </button>
                          </div>
                        </td>
                      </tr>
                    )}
                    <thead>
                      <tr>
                        <th>Sl</th>
                        <th>PVMS</th>
                        <th>Nomenclature</th>
                        <th>A/U</th>
                        <th>Contract Qty</th>
                        <th>Already Delivered</th>
                        <th></th>
                        <th>Remarks</th>
                        <th className='text-center'>Action</th>
                      </tr>
                    </thead>
                    <tbody>

                      {visibleIndices.map((index, key) => {
                        const val = workorderPvms[index];
                        console.log(workorderPvms);
                        if (!val) return null;
                        return (
                          <tr key={index}>
                            <td>{key + 1}</td>
                            {/* <td>{val.pvms_id}</td> */}
                            <td style={{ minWidth: '180px' }}>
                              <AsyncSelect
                                cacheOptions
                                defaultOptions={originalPvmsList.map(item => ({
                                  value: item.pvms_id,
                                  label: `${item.pvms_id} - ${item.pvms_old_name}`
                                }))}
                                loadOptions={loadPvmsOptions}
                                value={
                                  val.pvms_id
                                    ? {
                                      value: val.pvms_id,
                                      label: `${val.pvms_id} - ${val.pvms_old_name}`
                                    }
                                    : null
                                }
                                onChange={(selectedOption) =>
                                  handlePvmsSelect(selectedOption?.value, index)
                                }
                                placeholder="Search PVMS ID"
                                isClearable={false}
                              />
                            </td>


                            <td>
                              <textarea
                                className='form-control'
                                rows="2"
                                value={val.nomenclature}
                                readOnly
                              />
                            </td>
                            <td>{val.au}</td>
                            <td>{val.qty}</td>
                            <td>{val.total_received}</td>
                            <td>
                              <table>
                                <thead>
                                  <tr>
                                    <th>Receive Qty</th>
                                    <th>Batch</th>
                                    <th>Mfg Date</th>
                                    <th>Expiry Date</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {val.delivery_data.map((delivery_val, delivey_key) => (
                                    <tr key={delivey_key}>
                                      <td>
                                        <input
                                          type="text"
                                          className="form-control"
                                          name="delivery_qty"
                                          value={delivery_val.delivery_qty}
                                          onChange={(e) =>
                                            handleOnChangeReceivePvmsData(e.target, index, delivey_key)
                                          }
                                          placeholder={`due ${val.qty - val.total_received}`}
                                          required={delivery_val.batch_no || delivery_val.expire_date}
                                        />
                                      </td>
                                      <td>
                                        <input
                                          type="text"
                                          className="form-control"
                                          name="batch_no"
                                          value={delivery_val.batch_no}
                                          onChange={(e) =>
                                            handleOnChangeReceivePvmsData(e.target, index, delivey_key)
                                          }
                                          required={delivery_val.delivery_qty || delivery_val.expire_date}
                                        />
                                      </td>
                                      <td>
                                        <DatePicker
                                          name="mfg_date"
                                          className="form-control"
                                          selected={delivery_val.mfg_date}
                                          value={delivery_val.mfg_date}
                                          onChange={(date) =>
                                            handleOnChangeReceivePvmsMfgDate(date, index, delivey_key)
                                          }
                                          dateFormat="dd/MM/yyyy"
                                          autoComplete="off"
                                          required={delivery_val.delivery_qty || delivery_val.batch_no}
                                        />
                                      </td>

                                      <td>
                                        <DatePicker
                                          name="expire_date"
                                          className="form-control"
                                          selected={delivery_val.expire_date}
                                          value={delivery_val.expire_date}
                                          onChange={(date) =>
                                            handleOnChangeReceivePvmsExpireDate(date, index, delivey_key)
                                          }
                                          dateFormat="dd/MM/yyyy"
                                          autoComplete="off"
                                          required={delivery_val.delivery_qty || delivery_val.batch_no}
                                        />
                                      </td>
                                      <td>

                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>

                              <div className="d-flex justify-content-between mt-2">
                                <button
                                  type="button"
                                  className="btn btn-primary btn-sm"
                                  onClick={() => handleAddMoreDeliveryData(index)}
                                >
                                  + Add Batch
                                </button>
                              </div>
                            </td>
                            <td>
                              <textarea
                                className="form-control"
                                rows="1"
                                name="receiver_remarks"
                                placeholder="Remarks"
                                onChange={(e) => handleRemarksChange(e, index)}
                              />
                            </td>
                            <td>
                              <button
                                type="button"
                                className="btn "
                                onClick={() => handleRemoveRow(index)}
                              >
                                <i className='pe-7s-close-circle text-danger f20 font-weight-bold'></i>
                              </button>
                            </td>
                          </tr>
                        );
                      })}


                      {visibleIndices.length < workorderPvms.length && (
                        <tr>
                          <td colSpan="8">
                            <div className="d-flex justify-content-end">
                              <button
                                type="button"
                                className="btn btn-success"
                                onClick={handleAddMoreRow}
                              >
                                + Add More Item
                              </button>
                            </div>
                          </td>
                        </tr>
                      )}



                    </tbody>
                  </table>
                </div>

                <div className="col-lg-12">
                  <br />
                  <div className="mt-1">
                    <button type="submit" className="btn btn-primary mt-1">{buttonName()}</button>
                    {workorderReceiveApprovedBy == userApprovalRoleKey && <span className='ml-2 text-success'>Already Approved</span>}
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      )}
    </div>
  )
}

if (document.getElementById('react-workorder-receive')) {
  createRoot(document.getElementById('react-workorder-receive')).render(<CreateEdit />)
}
