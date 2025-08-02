import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Swal from 'sweetalert2';

export default function DemandDescision() {

    const [Details, setDetails] = useState('')
    const [BottomDetails, setBottomDetails] = useState('')
    const [isMunirKeyboard, setIsMunirKeyboard] = useState(false)
    const handleChangeIsMunirKeyboard = (value) => {
        setIsMunirKeyboard(value)
        if (value) {
            window.$('.ck-editor__main').addClass('munir-bangla')
        } else {
            window.$('.ck-editor__main').removeClass('munir-bangla')
        }

    }

    return (
        <>
            <div className='col-12 my-2'>
                <input type="hidden" name="is_munir_keyboard" value={isMunirKeyboard} />
                <div>
                    Top Details <span className="text-danger">*</span>{' '}
                    <input type='checkbox' onChange={(e) => handleChangeIsMunirKeyboard(e.target.checked)} checked={isMunirKeyboard} /> Munir Keyboard
                </div>
                <div className='mt-2'>
                    <CKEditor
                        name="top_details"
                        editor={ClassicEditor}
                        config={{
                            toolbar: ['undo', 'redo',
                                '|', 'heading',
                                '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                                '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                '|', 'blockQuote', 'insertTable', 'underline',
                                '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'alignment']
                        }}
                        data={Details}
                        onChange={(event, editor) => {
                            const data = editor.getData();
                            setDetails(data)
                        }}
                        required
                    />
                    <input type="hidden" name="top_details" value={Details} />
                </div>
                <div className='mt-2'>
                    Bottom Details <span className="text-danger">*</span>{' '}
                </div>
                <div className='mt-2'>
                    <CKEditor
                        name="bottom_details"
                        editor={ClassicEditor}
                        config={{
                            toolbar: ['undo', 'redo',
                                '|', 'heading',
                                '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                                '|', 'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                                '|', 'blockQuote', 'insertTable', 'underline',
                                '|', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'alignment']
                        }}
                        data={BottomDetails}
                        onChange={(event, editor) => {
                            const data = editor.getData();
                            setBottomDetails(data)
                        }}
                        required
                    />
                    <input type="hidden" name="bottom_details" value={BottomDetails} />
                </div>
            </div>
        </>
    )
}

if (document.getElementById('react-demand-decision')) {
    createRoot(document.getElementById('react-demand-decision')).render(<DemandDescision />)
}
