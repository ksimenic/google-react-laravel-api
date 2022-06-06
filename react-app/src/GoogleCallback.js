import React, {useState, useEffect} from 'react';
import {useLocation} from "react-router-dom";

function GoogleCallback() {

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [data, setData] = useState({});
    const [user, setUser] = useState(null);
    const location = useLocation();

    function fetchUserData() {
        fetch(`http://localhost:80/api/user`, {
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + data.access_token,
            }
        })
            .then((response) => {
                return response.json();
            })
            .then((data) => {
                setUser(data);
            })
            .catch((error) => {
                setLoading(false);
                setError(error);
            });
    }

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
        } else if (user != null) {
            return <DisplayUser user={user}/>
        } else {
            return (
                <div>
                    <DisplayData data={data}/>
                    <div style={{marginTop:10}}>
                        <button onClick={fetchUserData}>Fetch User</button>
                    </div>
                </div>
            );
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

function DisplayUser(user) {
    return (
        <div>
            <samp>{JSON.stringify(user, null, 2)}</samp>
        </div>
    );
}

export default GoogleCallback;