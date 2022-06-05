import React, {useState, useEffect} from 'react';
import {useLocation} from "react-router-dom";

function GoogleCallback() {

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [data, setData] = useState({});
    const location = useLocation();


    useEffect(() => {

        fetch(`http://localhost:80/api/auth/callback${location.search}`, {
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then((response) => {
                return response.json();
            })
            .then((data) => {
                setLoading(false);
                setData(data);
            })
            .catch((error) => {
                setLoading(false);
                setError(error);
            });
    }, []);

    if (loading) {
        return <DisplayLoading/>
    } else {
        if (error !== null) {
            return <DisplayError error={error}/>
        } else {
            return <DisplayData data={data}/>
        }
    }
}

function DisplayLoading() {
    return <div>Loading....</div>;
}

function DisplayError(error) {
    return (
        <div>
            <samp>{error.error.toString()}</samp>
        </div>
    );
}

function DisplayData(data) {
    return (
        <div>
            <samp>{JSON.stringify(data, null, 2)}</samp>
        </div>
    );
}

export default GoogleCallback;