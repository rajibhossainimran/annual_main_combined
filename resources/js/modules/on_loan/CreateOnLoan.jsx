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

export default function CreateOnLoan() {
    const [ReferenceNo, setReferenceNo] = useState()
    const [ReferenceDate, setReferenceDate] = useState()
    const [selectedVendor, setSelectedVendor] = useState();
    const [OnLoanPvms, setOnLoanPvms] = useState([]);
    const [isFormSubmited, setIsFormSubmited] = useState(false)

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

    const handleChangeSelectVendor = (item, select) => {
        setSelectedVendor(item.data);
    }

    const handleChangePvmsVal = (target, csr, index) => {
        OnLoanPvms[index] = { ...OnLoanPvms[index], [target.name]:target.value }
        setOnLoanPvms([...OnLoanPvms])
      }

    const handleChangePvms = (value) => {
        const pvms_exists = OnLoanPvms.find(i => i.id==value.value)

        if(pvms_exists) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `This pvms has been already added.`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        } else {
        const { id,nomenclature,unit_name, pvms_id, item_typename } = value.data

        const new_demand_pvms = {
            id,
            pvms_id: id,
            nomenclature,
            au:unit_name?.name,
            qty:'',
            remarks:''
        }

        setOnLoanPvms((prev) => {
            let copy = [...prev]
            copy.push(new_demand_pvms)

            return copy
        })
        }
    }

    const handleSubmit = (e) => {
        e.preventDefault();
        if(OnLoanPvms.length == 0) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `No PVMS added.`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        }

        if(!selectedVendor) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `Vendor is Required!`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        }

        const requestData = {
            vendor_id: selectedVendor.id,
            reference_no: ReferenceNo,
            reference_date: ReferenceDate,
            on_loan_items: OnLoanPvms
        }
        setIsFormSubmited(true);
        axios.post(`${window.app_url}/on-loan`, requestData)
        .then((res) => {
            window.location.href = '/on-loan'
        }).error(() => {
            setIsFormSubmited(false);
        })

    }

    return (
        <form onSubmit={handleSubmit}>
            <div className='row'>
                <div className='col-6 mb-2'>

                <b>Date <span className="requiredStar">*</span>: </b>
                <div>
                    <DatePicker
                        className="form-control"
                        selected={ReferenceDate}
                        onChange={(date) => setReferenceDate(date)}
                        dateFormat="dd/MM/yyyy"
                        required
                    />
                </div>
                </div>
                <div className='col-6 mb-2'>
                    <b>Reference No <span className="requiredStar">*</span>: </b>
                    <input className='form-control' type='text' onChange={(e) => setReferenceNo(e.target.value)} value={ReferenceNo} required/>
                </div>
                <div className='col-6 mb-2'>
                    <b>Vendor <span className="requiredStar">*</span>: </b>
                    <AsyncSelect cacheOptions name='vendor_id' loadOptions={loadVendorOptions} onChange={handleChangeSelectVendor} value={{value: selectedVendor?.id, label:selectedVendor?.name, data:selectedVendor}} placeholder="Vendor Name" defaultOptions required />
                </div>
                <div className="col-12">
                <table className="table">
                    <thead>
                    <tr>
                        <th>Sl</th>
                        <th>PVMS</th>
                        <th>Nomenclature</th>
                        <th>A/U</th>
                        <th>Quantity</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody>
                    {OnLoanPvms.map((val, key) => (
                        <tr key={key}>
                        <td>{key + 1}</td>
                        <td>{val.pvms_id}</td>
                        <td>{val.nomenclature}</td>
                        <td>{val.au}</td>
                        <td>
                            <input type='text' className='form-control' name='qty' value={val.qty} onChange={(e) => handleChangePvmsVal(e.target, val, key)} autoComplete='false' required/>
                        </td>
                        <td>
                            <textarea className='form-control' name='remarks' value={val.remarks} onChange={(e) => handleChangePvmsVal(e.target, val, key)}></textarea>
                        </td>
                        </tr>
                    ))}

                    </tbody>
                </table>
                </div>
            </div>

            <div className='row my-3'>
                <div className='col-md-12 gap-2'>
                    <b className='mb-2'>Search PVMS : On Loan PVMS Item</b>
                    <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangePvms} value={''} defaultOptions placeholder="PMVS No" />
                </div>
            </div>
            <button className='btn btn-success' type='submit'
            disabled={isFormSubmited}
            >
                {isFormSubmited ? 'Saving...' : 'Save & Forward'}
            </button>
        </form>
    )
}
if (document.getElementById('react-create-on-loan')) {
    createRoot(document.getElementById('react-create-on-loan')).render(<CreateOnLoan />)
}
