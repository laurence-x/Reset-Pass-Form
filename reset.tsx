import { useEffect, useRef, useState } from "react"
import { useLocation, useNavigate } from "react-router-dom"
import CapsOn from "../../fns/capsOn"
import checks from "./checks"
import ku from "./keyup"
import preCheck from "./precheck"

export default function Res() {
	const nvg = useNavigate()
	const Pw = useRef<HTMLInputElement>(null!)
	const Pw2 = useRef<HTMLInputElement>(null!)
	const ms = useRef<HTMLParagraphElement>(null!)
	const iB = useRef<HTMLInputElement>(null!)
	const rD = useRef<HTMLDivElement>(null!)

	const query = new URLSearchParams(useLocation().search)
	const h = String(query.get("h"))
	h === null && nvg("/", { replace: true })
	let hdc = decodeURIComponent(h)

	useEffect(() => {
		preCheck(hdc, rD, nvg)
	}, [ hdc, nvg ])

	const [ showPw, setShowPw ] = useState(false)
	const showPwTgl = () => setShowPw((showPw) => !showPw)

	const kup = () => ku(Pw, Pw2, ms, iB)
	const btn = () => checks({ Pw, Pw2, ms, iB, rD, nvg, hdc })

	return (
		<>
			<CapsOn />
			<b className="h">Reset</b>
			<div className="l c" ref={rD}>
				<input
					type={showPw ? "text" : "password"}
					name="pass"
					ref={Pw}
					onKeyUp={kup}
					placeholder="type a new password..."
					title="type a new password"
					pattern=".{6,20}"
					minLength={Number(6)}
					maxLength={Number(20)}
					autoComplete="off"
					required
				/>
				<br />
				<input
					type={showPw ? "text" : "password"}
					name="pass2"
					ref={Pw2}
					onKeyUp={kup}
					placeholder="re-type the password..."
					title="type your password"
					pattern=".{6,20}"
					minLength={Number(6)}
					maxLength={Number(20)}
					autoComplete="off"
					required
				/>
				<label className="showPw" htmlFor="checkbox">
					<div>Show passwords?</div>
					<div>
						<input
							id="checkbox"
							type="checkbox"
							checked={showPw}
							onChange={showPwTgl}
						/>
					</div>
				</label>
				<br />
				<b ref={ms} className="hide c r"></b>
				<input type="button" ref={iB} value="reset" onMouseUp={btn} />
			</div>
		</>
	)
}
