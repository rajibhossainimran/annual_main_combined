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
import Track from '../../componants/Track';

export default function Tracking() {
  const [TrackingModule,setTrackingModule] = useState('demand');


return (
    <>
        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
            <h5 className="f-14">Tracking</h5>
        </div>
        <div className="my-2 px-4">
            <div className='d-flex align-items-center mb-2'>
                <div className='pr-2'>
                    <label>Tracking</label>
                </div>
                <div>
                    <select className="form-control" value={TrackingModule} onChange={e => setTrackingModule(e.target.value)}>
                        <option value="demand">Demand</option>
                        <option value="notesheet">Notesheet</option>
                        <option value="csr">Csr</option>
                    </select>
                </div>
            </div>
            <Track TrackingModule={TrackingModule}/>
        </div>
    </>
)}

if (document.getElementById('react-tracking')) {
    createRoot(document.getElementById('react-tracking')).render(<Tracking />)
}
