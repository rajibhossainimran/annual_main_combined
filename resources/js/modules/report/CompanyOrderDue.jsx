import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import moment from 'moment';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";

export default function CompanyOrderDue() {
    const [financialYears, setFinancialYears] = useState([]);
    const [financialYear, setFinancialYear] = useState('');
    const [selectedVendor, setSelectedVendor] = useState();
    const [contractNumber, setContractNumber] = useState();

    useEffect(() => {
        axios.get(`${window.app_url}/settings/financial-years/api`)
        .then((res) => {
            setFinancialYears(res.data)
        })
    },[])

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

      const handleChangeSelectVendor = (option, select) => {
        debugger

        const hiddenInput = document.querySelector('input[name="vendor"]');
        if (hiddenInput) {
            hiddenInput.value = option ? option.value : '';
        }

        $(document).trigger('vendorSelectChange', [option]);
    }

return (
    <div className='row p-2'>
        <div className='col-lg-6'>
            <label>Financial Years</label>
            <select className='form-control' id="fy" name="fy" required value={financialYear} onChange={(e) => setFinancialYear(e.target.value)}>
                <option value="">Select</option>
                {financialYears.map((val, key) => (
                <option key={key} value={val.id}>{val.name}</option>
                ))}
            </select>
        </div>
        <div className="col-lg-6">
            <div className="form-group">
            <label>Company Name</label>
            <AsyncSelect cacheOptions name='vendor' id="vendor" loadOptions={loadVendorOptions} onChange={handleChangeSelectVendor} placeholder="Company Name" defaultOptions isClearable={true}/>
            </div>
        </div>
    </div>
)}

if (document.getElementById('react-company-order-due')) {
    createRoot(document.getElementById('react-company-order-due')).render(<CompanyOrderDue />)
}
